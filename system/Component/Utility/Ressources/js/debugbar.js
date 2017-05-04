
var button = $("._bp-toggle-btn");
var debugbar = $("._bp-debug-bar");
var container = $("._bp-debug-content");
var opened = false;

button.on('click', function(event) {

	if(opened == false)
	{
	  	debugbar.animate({
            "bottom" : "200px",
            "duration": 400,
            "easing": "linear",
            'callback': function(opts) {
                // ...
            }
        });
        container.animate({
            "height" : "200px",
            "duration": 400,
            "easing": "linear",
            'callback': function(opts) {
                container.html('Some text and markup');
            }
        });

        button.addClass('opened');
        opened = true;
  	} else {
	  	debugbar.animate({
            "bottom" : "0px",
            "duration": 400,
            "easing": "linear",
            'callback': function(opts) {
                // ...
            }
        });
        container.animate({
            "height" : "0px",
            "duration": 400,
            "easing": "linear",
            'callback': function(opts) {
                // ...
            }
        });
        
        button.removeClass('opened');
        opened = false;
  	}

});