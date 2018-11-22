<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$modules = [];

$modules['system']['get']['modules'] = [
    'ignore_token' => true
];

$modules['chess']['post']['delegate'] = [
    'delegator' => [
        'required' => true,
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_USERID => [
                \Validators::VALIDATOR_ID => true
            ]
        ],
        'filters' => [
            \Filters::FILTER_FUNCTION_MODEL => [
                \Filters::FILTER_MODEL_NAME => \Collections::USERS
            ]
        ]
    ]
];

$modules['game']['post']['host'] = [
    'scheduled' => [
        'required' => true,
        'filters' => [
            \Filters::FILTER_FUNCTION_INT => []
        ]
    ],
    'force' => [
    ]
];

$modules['team']['post']['accept'] = [
    'invite' => [
        'required' => true,
        'validators' => [
            \Validators::VALIDATOR_VALID_ENTITY => [
                \Validators::VALIDATOR_ENTITY => \Collections::INVITATIONS
            ]
        ],
        'filters' => [
            \Filters::FILTER_FUNCTION_MODEL => [
                \Filters::FILTER_MODEL_NAME => \Collections::INVITATIONS
            ]
        ]
    ]
];

$modules['team']['post']['invite'] = [
    'team' => [
        'required' => true,
        'validators' => [
            \Validators::VALIDATOR_VALID_ENTITY => [
                \Validators::VALIDATOR_ENTITY => \Collections::TEAMS
            ]
        ],
        'filters' => [
            \Filters::FILTER_FUNCTION_MODEL => [
                \Filters::FILTER_MODEL_NAME => \Collections::TEAMS
            ]
        ]
    ],
    'users' => [
        'required' => true,
        'array' => true,
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_USERID => [
                \Validators::VALIDATOR_ID => true
            ]
        ],
        'filters' => [
            \Filters::FILTER_FUNCTION_MODEL => [
                \Filters::FILTER_MODEL_NAME => \Collections::USERS
            ]
        ]
    ]
];

$modules['team']['post']['create'] = [
    'players' => [
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_USERID => [
                \Validators::VALIDATOR_ID => true
            ]
        ],
        'filters' => [
            \Filters::FILTER_FUNCTION_MODEL => [
                \Filters::FILTER_MODEL_NAME => \Collections::USERS
            ]
        ],
        'array' => true
    ]
];

$modules['users']['post']['login'] = [
    'ignore_token' => true,
    'username' => [
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_LENGTH => [
                \Validators::VALIDATOR_LENGTH_MIN => 3,
                \Validators::VALIDATOR_LENGTH_MAX => 64
            ],
        ],
        'required' => true
    ],
    'password' => [
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_LENGTH => [
                \Validators::VALIDATOR_LENGTH_MIN => 6
            ]
        ],
        'required' => true
    ]
];

$modules['users']['post']['logout'] = [
    'ignore_token' => true,
    'token_id' => [
        'required' => true       
    ]
];

$modules['users']['post']['register'] = [
    'ignore_token' => true,
    'username' => [
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_LENGTH => [
                \Validators::VALIDATOR_LENGTH_MAX => 64,
                \Validators::VALIDATOR_LENGTH_MIN => 3
            ],
        ],
        'required' => true
    ],
    'password' => [
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_LENGTH => [
                \Validators::VALIDATOR_LENGTH_MIN => 6,
            ]
        ],
        'required' => true
    ],
    'repassword' => [
        'required' => true
    ],
    'email' => [
        'required' => true,
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_EMAIL => []
        ]
    ],
    'firstname' => [
        'required' => true,
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_LENGTH => [
                \Validators::VALIDATOR_LENGTH_MIN => 3,
                \Validators::VALIDATOR_LENGTH_MAX => 16
            ]
        ]
    ],
    'lastname' => [
        'required' => true,
        'validators' => [
            \Validators::VALIDATOR_FUNCTION_LENGTH => [
                \Validators::VALIDATOR_LENGTH_MIN => 3,
                \Validators::VALIDATOR_LENGTH_MAX => 16
            ]
        ]
    ]
];

return $modules;
