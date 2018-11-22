<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Controllers;

use Backend\Abstraction\Controllers;
use Backend\Abstraction\Collection;

/**
 * Description of Team
 *
 * @author Aleksandar
 */
class Team extends Controllers {

    public function postCreate() {
        $players = [];

        foreach ($this->post->players as $player) {
            if (in_array($player->id, $players)) {
                continue;
            }

            $players[] = $player->id;
        }

        $now = time();
        $uniqueid = uniqid(round(microtime(true)), TRUE);

        $team = new \Models\Team([
            'leader' => $this->post->token->id,
            'players' => json_encode($players),
            'uniqueid' => $uniqueid,
            'created' => $now,
            'updated' => $now
        ]);

        $team->save();

        $this->endMessage = $team;
        $this->endStatus = 200;
    }

    public function postInvite() {
        $invitedBy = $this->post->token;
        $team = $this->post->team;
        $users = $this->post->users;
        $response = [];

        foreach ($users as $user) {
            if (in_array($user->id, $team->players)) {
                $response['players'][] = "User {$user->username} already in team";
                continue;
            }

            $inviteArray = [
                'user' => $user->id,
                'invited_by' => $invitedBy->id,
                'team' => $team->id
            ];

            $invite = new \Models\Invitation($inviteArray);
            $invite->save();

            if ($invite->valid()) {
                $response['players'][] = "Invitation sent to {$user->username}";
                continue;
            }

            $response['players'][] = "Error while sending invitation to {$user->username}";
        }

        $response['team'] = $team->extract();
        $this->endMessage = $response;
        $this->endStatus = 200;
    }

    public function postAccept() {
        $invite = $this->post->invite;
        $user = $this->post->token;

        if ($invite->user !== $user->id) {
            $this->endMessage = 'You don\'t have permission to accept this';
            $this->endStatus = 401;
        }

        $teamHelper = Collection::instance(\Collections::TEAMS);
        $team = $teamHelper->getOne([
            'id' => $invite->team
        ]);

        if (in_array($user->id, $team->players)) {
            $this->endMessage = 'You are already in this team';
            $this->endStatus = 401;
        }

        $team->players[] = $user->id;
        $team->save();

        if (!$team->valid()) {
            $msg = 'There were some errors while updating, contact developers';
            $this->endMessage = $msg;
            $this->endStatus = 500;
        }

        $invite->user = -1;
        $invite->save();

        $this->endMessage = $team->extract();
        $this->endStatus = 200;
    }

}
