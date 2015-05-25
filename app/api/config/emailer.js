    var nodemailer = require('nodemailer');
    var logger = require("./logger");

    module.exports = function(smtpService,smtpUser,smtpPassword) {
// Create a SMTP transport object
var transport = nodemailer.createTransport("SMTP", {
        service: smtpService,
        auth: {
            user: smtpUser,
            pass: smtpPassword
        }
    });
         logger.info('SMTP Configured');

        return function(name,email,mailsubject,message,next) {      
          
          var testMail = {
// sender info
	           sender: 'Prayitforward<noreply@prayitforward.co>',

 // Comma separated list of recipients
              to:  name + '  <'+email +'>',

              // Subject of the message
              subject: mailsubject,

               // html body
              //html:'<p><b>PIF</b></p>'+'<p>Here\'s a test mail <br/></p>'
              html : message

                   // An array of attachments
              //attachments:[]
};

          transport.sendMail(testMail, next);   
        }
      };

