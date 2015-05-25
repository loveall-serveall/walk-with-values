var winston = require('winston');
winston.emitErrs = true;

var server_name='default';
if(process.argv.indexOf("-n") != -1){ //does our flag exist?
    server_name = process.argv[process.argv.indexOf("-n") + 1]; //grab the next item
}

var logger = new winston.Logger({
    transports: [
        new winston.transports.File({
            level: 'info',
            filename: './logs/all-logs-'+server_name+'.log',
            handleExceptions: true,
            json: true,
            maxsize: 5242880, //5MB
            maxFiles: 5,
            colorize: true
        }),
        new winston.transports.Console({
            level: 'debug',
            handleExceptions: true,
            json: false,
            colorize: true
        })
    ],
    exitOnError: false
});

module.exports = logger;
module.exports.stream = {
    write: function(message, encoding){
        logger.info(message);
    }
};
