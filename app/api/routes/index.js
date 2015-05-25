var express = require('express');
var router = express.Router();

var users = require('./users.js');
var util = require('./util.js');

/*
 * Routes that can be accessed by any one
 */
//router.post('/sadhanaregistration', users.sadhanaReg); //Registers a sadhana for a user
router.post('/users', users.create);
router.post('/users/auth', users.auth);
router.put('/pchange/:userid', users.pchange);
router.post('/sadhanaregistration', users.sadhanaUpdate); //Registers a sadhana for a user
//router.get('/websadhanalist/:userid', users.sadhanalist);

router.post('/sadhanaupdate', users.sadhanaUpdate);
router.get('/sadhanalist/:userid', users.sadhanalist);
//router.get('/allsadhanas/:userid', users.sadhanalist);
router.post('/sadhanas/:userid/:fordate', users.sadhanasTracked);
router.get('/sadhanastrack/:userid/:fordate', users.sadhanasTrack);
//router.get('/webscreenTrack/:userid/:fordate', users.sadhanasTrack);
router.post('/webscreenTracked/:userid/:fordate', users.webscreenTracked);
router.get('/signedup/:userid', users.signedup);
router.post('/questions/:userid', users.createQuestion);
router.get('/totalSadhakas', users.progressCount);
//router.put('/sadhanaupdate', users.sadhanaUpdate); //Updates the sadhanas for a user
router.get('/restapi/:param',util.testParam);
router.post('/log', util.log);
router.get('/sadhanas', util.sadhanas);
router.get('/values', util.values);


//router.post('/sadhanas/:userid', users.sadhanasTracked);
//router.get('/users', users.getAll);
router.get('/tbd', util.testDb);
//router.get('/users/:id', users.getOne);
//router.get('/webscreenTrack/:userid/:fordate', users.webscreenTrackCustom);





/*
 * Routes that can be accessed only by autheticated users
 */
//router.get('/api/v1/products', products.getAll);

/*
 * Routes that can be accessed only by authenticated & authorized users
 */
//router.get('/api/v1/admin/users', users.getAll);

module.exports = router;
