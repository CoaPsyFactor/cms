var geng = function() {
};

geng.prototype.get = function(module, method, data, callback) {

    if (!data) {
	data = {};
    }

    data.module = module;
    data.method = method;
    data.type = 'get';

    geng.prototype.ajaxCall(data, callback);
};

geng.prototype.post = function(module, method, data, callback) {

    if (!data) {
	data = {};
    }

    data.module = module;
    data.method = method;
    data.type = 'post';

    geng.prototype.ajaxCall(data, callback);
};

geng.prototype.ajaxCall = function(data, callback) {
    if (!data) {
	data = {type: 'get'};
    }

    if (!data.module || !data.method) {
	throw new Error('Both module and method must be defined');
    }

    var type = data.type;
    delete data.type;
    $.ajax({
	url: 'index.php?api=true',
	type: type,
	data: $.param(data),
	dataType: 'json',
	success: function(msg) {
	    if (typeof callback === 'function') {
		callback(undefined, msg);
	    }
	},
	error: function(error, status) {
	    if (typeof callback === 'function') {
		callback(error.responseJSON, {});
	    }
	}
    });
};

var g = new geng();