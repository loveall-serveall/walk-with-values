var http = require('http');
var logger = require("../init/logger");
//var t20qAppconfig = require('config').get('20qApp');
var sparkpost = require('sparkpost')({
            key: '6a221c0860bf79f716dd8bf4003e29c7c0268a77'
        });

var pushutils = {

  sendSparkMail : function(question) {
    //logger.info("Sending a SPARK mail using",mailObject)
    var trans = {};

    // Set some metadata for your email
    trans.campaign = 'Walk With Values';
    //trans.from = '20 Questions';

    trans.from = 'TeamWWV <sandbox@sparkpostbox.com>';
  //  trans.from = 'Team WWV <walkwithvalues@gmail.com>';
    
    var _subject;
    var _html;
    var _text;

        _subject = 'Sadhaka\'s Submitted a question/feedback ' ;
        _html = 'Sairam  &nbsp;&nbsp;TeamWWV'+
'<br><br>'+ question.user_email +
     ' contacted us with following information. <br>'+
    '<br> <strong>   '+question.question+'  </strong>  <br>'+
 ' Please respond if necessary. User Email Id :  '+question.user_email+ '  <br> <br>'+
'****** This message is generated by the system. &nbsp;&nbsp; Please do not reply to this mail. '+
'<br><br> Sairam.'  ;
                  
        _text = 'Sairam  &nbsp;&nbsp;TeamWWV'+
'<br><br>'+ question.user_email +' contacted us with following information. <br>'+
    '<br> <strong>   '+question.question+'  </strong>  <br>'+
 ' Please respond if necessary. User Email Id :' + question.user_email + '<br> <br>'+
'****** This message is generated by the system. &nbsp;&nbsp; Please do not reply to this mail. '+
'<br><br> Sairam.'  ;

    
trans.subject = _subject;

// Add some content to your email
trans.html = _html;
trans.text = _text;
trans.substitutionData = {
    name: 'Walk With Values'
};

// Pick someone to receive your email
trans.recipients = [{
    address: {
        name: 'Team WWV',
        email: 'lakshman537@gmail.com'
    }
},{
    address: {
        name: 'Team WWV',
        email: 'shettyjm2014@gmail.com'
    }
},{
    address: {
        name: 'Team WWV',
        email: 'walkwithvalues@gmail.com'
    }
}];

// Send it off into the world!
sparkpost.transmission.send(trans, function(err, res) {
    if (err) {
        logger.info('Whoops! Something went wrong');
        logger.info(err);
    } else {
        logger.info('Woohoo! You just sent your first mailing!');
    }
});


 //logger.info("Sent  a SPARK mail .Please check your mail account",mailObject)

},
sendEmail: function() {
        logger.info('**********SendMail Invoked********** prid');

        var data = {
            "options": {
                "open_tracking": true,
                "click_tracking": true
            },
            "return_path": "bounces-test@20q.club",
            "metadata": {
                "user_type": "students"
            },
            "substitution_data": {
                "subkey": "subvalue"
            },
            "recipients": [

                {
                    "address": "michael.seyoum1@gmail.com"
                }
            ],
            "content": {
                "from": {
                    "name": "20questions",
                    "email": "noreplay@20q.club"
                },
                "subject": "[20 Questions] Question for you from Jagadeesh",
                "reply_to": "Awesome Company ",
                "text": "Hi {{toName}},\r\n{{fromName}} is asking: I {{toName}}?\r\n(You were thinking spinach dhal. The hint you gave was Food.)",
                "html": "<strong> Hi {{toName}},</strong><p>  {{fromName}} is asking: I {{toName}}? <br/> (You were thinking spinach dhal. The hint you gave was Food.) </p>"
            }
        };

        var dataString = JSON.stringify(data);

        var headers = {
            'Authorization': 'fb12c2ed842583cda35128aa783aba9e38716664',
            'Content-Type': 'application/json',
            'Content-Length': dataString.length
        };

        var options = {
            host: 'api.sparkpost.com',
            port: 443,
            path: '/api/v1/transmissions',
            method: 'POST',
            headers: headers
        };

        httpPoster(options, dataString);

    },
    pushGcm: function(regId, pmessage, prid, callback) {
        logger.info('**********GCM push notification invoked********** prid', prid);
        // logger.info('push Notification invoked for regId ',regId ,'message' ,pmessage);

        var data = {
            "collapse_key": "pifappnf",
            "delayWhileIdle": true,
            "timeToLive": 3,
            "data": {
                "message": "Prayit Forward " + pmessage,
                "title": pmessage,
                "prid": prid
            },
            "registration_ids": [regId]
        };

        var dataString = JSON.stringify(data);

        var headers = {
            'Authorization': 'key=' + t20qAppconfig.pushconfig.gcmconfig.key,
            'Content-Type': 'application/json',
            'Content-Length': dataString.length
        };

        var options = {
            host: 'android.googleapis.com',
            port: 80,
            path: '/gcm/send',
            method: 'POST',
            headers: headers
        };

        //Setup the request 
        var req = http.request(options, function(res) {
            res.setEncoding('utf-8');

            var responseString = '';

            res.on('data', function(data) {
                responseString += data;
            });

            res.on('end', function() {
                var resultObject = JSON.parse(responseString);
                logger.info(responseString);
                logger.info(resultObject);
            });
            logger.info('STATUS: ' + res.statusCode);
            logger.info('HEADERS: ' + JSON.stringify(res.headers));

        });

        req.write(dataString);
        req.end();
        req.on('error', function(e) {
            // TODO: handle error.
            logger.error('error : ' + e.message + e.code);
        });

    }
};

function httpPoster(options, dataString) {

    //Setup the request 
    var req = http.request(options, function(res) {
        res.setEncoding('utf-8');

        var responseString = '';

        res.on('data', function(data) {
            responseString += data;
        });

        res.on('end', function() {
            var resultObject = JSON.parse(responseString);
            logger.info(responseString);
            logger.info(resultObject);
        });
        logger.info('STATUS: ' + res.statusCode);
        logger.info('HEADERS: ' + JSON.stringify(res.headers));

    });

    req.write(dataString);
    req.end();
    req.on('error', function(e) {
        // TODO: handle error.
        logger.error('error : ' + e.message + e.code);
    });
};


module.exports = pushutils;