define([], function () {
    getTimeFormated = function(time, format) {
        if (typeof format == 'undefined'){
            format = '%d %h:%m:%s'
        }
        if (time<=0){
            return 'Finished';
        }
        var days = Math.floor(time/86400);
        var hours = Math.floor((time - 86400*days)/3600);
        var minutes = Math.floor((time - 86400*days - 3600*hours)/60);
        var seconds = time%60;
        if(format) {
            return format.replace(/%d|%h|%m|%s/gi, function (x) {
                switch (x) {
                    case '%d':
                        return days?days:'0';
                    case '%h':
                        return hours?hours:'00';
                    case '%m':
                        return minutes?minutes:'00';
                    case '%s':
                        return seconds?seconds:'00';

                }
            });
        }
        return '';
    };

    return function(config, node)
    {
        // retrieve the value from the span
        var sec = config.time_left;
        var timer = setInterval(function() {
            node.innerText = getTimeFormated(--sec);
            if (sec <= 0) {
                clearInterval(timer);
            }
        }, 1000);
    };
});