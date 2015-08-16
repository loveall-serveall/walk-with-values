
/*
 Soul of WWV APIs.
*/

var logger = require("../init/logger");
var utils = require("./util");
var moment = require('moment');
var _ = require('underscore');

var bcrypt = require('bcrypt-nodejs');
var pushutils = require("./pushutils");
var start = process.hrtime();


var users = {

    // track a date specific progress from the website for a user
    // post - /webscreenTracked/:userid/:fordate
    // current payload 
    /*{"sdate":"08/09/2015",
      "common_list":["1","5","9"],
      "custom_list":"",
      "healthybody_goal":"",
      "healthybody_subgoals":"",
      "healthymind_goal":"",
      "healthymind_subgoals":"",
      "hb_cbox":"10",
      "hm_cbox":"11",
      "human_values":"",
      "user_defined_values":"",
      "hv_rbtns":"",
      "user_defined_hv_rbtns":""}
    */
    webscreenTracked: function(req, res) {
        logger.info("{ webscreenTracked - \n for userid   {"+req.params.userid+"} invoked")
        var fordateRequest = req.params.fordate;
        var fordate = moment().format('YYYY-MM-DD');

        if (fordateRequest == null || fordateRequest == "null" || fordateRequest.length < 1) {
            fordateRequest = fordate;
        }
        
        logger.info('userid', req.params.userid, " sadhanlist : ", "fordateRequest :", fordateRequest);

        logger.info("payload == ",JSON.stringify(req.body.fields));

        // update process
        // 1. read curent tracking.
        req.db.collection('sadhakas').find({
            "userid": parseInt(req.params.userid),
            "track.fordate": fordateRequest
        }, {
            "id": 1,
            "track.fordate.$": 1
        }).limit(1).toArray(function(err, mytrack) {




            var newmySadhanasTracked = [{
                    type: 1,
                    tracked: []
                }, {
                    type: 2,
                    tracked: []
                }, {
                    type: 3,
                    tracked: []
                }, {
                    type: 4,
                    tracked: []
                },
                {
                    type: 5,
                    tracked: []
                },
                {
                    type: 6,
                    tracked: []
                }

            ];


            
            var type2tracks = [];
            //var type3tracks = [];
            //var type4tracks = [];
          //  var type5tracks = [];

            if (mytrack.length == 1 
                && mytrack[0].track.length == 1) {

                // merge the track data

                //   logger.info("data specific track exists",mytrack[0].track[0]);

                
                type2tracks = mytrack[0].track[0].tracked[1].tracked;
              //  type3tracks = mytrack[0].track[0].tracked[2].tracked;
              //  type4tracks = mytrack[0].track[0].tracked[3].tracked;
            //    type5tracks = mytrack[0].track[0].tracked[4].tracked;
                
                newmySadhanasTracked[1].tracked = type2tracks;
               // newmySadhanasTracked[2].tracked = type3tracks;
              //  newmySadhanasTracked[3].tracked = type4tracks;
              //  newmySadhanasTracked[4].tracked = type5tracks;

                //logger.info("type1tracks",type1tracks);



            } else {
                logger.info("data specific track does not exists");

                // insert the new row..
            }
            //logger.info("Track for the userid is ",mytrack[0]);

            // 2. read screen changes.


            // 3. merge tracks . remove omits/add new.
            // 4. pull curent tracks
            // 5. push new track .
            //[{"type":1,"tracked":[{"id":5,"checked":true}]}]


            //logger.info(JSON.stringify(req.body[0]));


           var fields = req.body.fields;
            
           newmySadhanasTracked[0].tracked = _.map(req.body.fields.common_list,
           
            function(obj, key) {
               // console.log("obj", obj);
                return parseInt(obj);
            });

            if (fields.hb_cbox != null && fields.hb_cbox != '') {
            //logger.info("hb checked");
            newmySadhanasTracked[1].tracked =[10];

            }
            if (fields.hm_cbox != null && fields.hm_cbox != '') {
            // logger.info("hm checked");
             newmySadhanasTracked[2].tracked =[11];

            }

            newmySadhanasTracked[3].tracked = _.map(req.body.fields.custom_list,
           
            function(obj, key) {
               // console.log("obj", obj);
                return parseInt(obj);
            });


            newmySadhanasTracked[4].tracked = _.map(req.body.fields.common_value_score_list,
           
            function(obj, key) {
                console.log("obj", obj, " key" ,key);
                var valueTrack = {"id":parseInt(key),"scale":parseInt(obj)}

                //return parseInt(valueTrack);
                return valueTrack;
            });


           newmySadhanasTracked[5].tracked = _.map(req.body.fields.custom_value_score_list,
           
            function(obj, key) {
                console.log("obj", obj, " key" ,key);
                var valueTrack = {"id":parseInt(key),"scale":parseInt(obj)}

                //return parseInt(valueTrack);
                return valueTrack;
            });
   
       


            console.log("mySadhanasTracked", newmySadhanasTracked);
            // console.log("mySadhanasTracked" ,_.pluck(mySadhanasTracked,'id'));



            // first remove current day record
            req.db.collection('sadhakas').update({
                    "userid": parseInt(req.params.userid)
                }, {
                    $pull: {
                        "track": {
                            "fordate": fordateRequest
                        }
                    }
                },
                function(err, data) {
                    if (err) logger.error(err);
                    else {
                        logger.info('********* updated for date  ( ' + fordateRequest + ' ) record removed *******');
                        // add a notification
                    }

                    var todaysupdate = {
                        "fordate": fordateRequest,
                        "tracked": newmySadhanasTracked
                    };

                    req.db.collection('sadhakas').update({
                            "userid": parseInt(req.params.userid)
                        }, {
                            $push: {
                                "track": todaysupdate
                            }
                        }, {
                            new: true
                        },
                        function(err, data) {
                            if (err) logger.error(err);
                            else {
                                logger.info('********* todays( ' + fordate + ' ) record updated *******');
                                // add a notification
                            }



                            res.end('{"status" : "good"}');
                            logger.info("\n     }");
                        });



                    res.end('{"status" : "good"}');
                    logger.info("           } \n");
                });


        });

    },

    // for a given userd/given date get the sadhanas + track 
    // get /sadhanastrack/:userid/:fordate
    sadhanasTrack: function(req, res) {
        var fordate = moment().format('YYYY-MM-DD');
        var qrydate = (req.params.fordate != null) ? req.params.fordate : fordate;

        logger.info("{ sadhanasTrack - \n tracking for "+ req.params.userid + " "+qrydate)
        //logger.info("qrydate", qrydate);
        
        /*req.db.collection('sadhakas').find({
            "userid": parseInt(req.params.userid)
        }, {
            "sadhanaregistrations": 1
        }).toArray(
            function(err, usrsadhanasregs) {*/
           sadhakaSadhanaList(req, function(usrsadhanasregs){ 
                //if (err) logger.info(err);
                //else {
                    // logger.info('data ----+' ,data[0].sadhanas);
                    var sadhanasregs = usrsadhanasregs[0].sadhanaregistrations;
                    //var commonsadhanas = sadhanasregs[0].sadhanas; // The 9 common sadhanas


                    req.db.collection('sadhakas').find({
                        "userid": parseInt(req.params.userid),
                        "track.fordate": qrydate
                    }, {
                        "id": 1,
                        "track.fordate.$": 1
                    }).limit(1).toArray(function(err, fordatetrack) {

                        //logger.info("Track for the userid is ",JSON.stringify(fordatetrack[0]));

                        var type1tracks = [];
                        var type2tracks = [];
                        var type3tracks = [];
                        var type4tracks = [];
                        var type5tracks = [];
                        var type6tracks = [];

                        if (fordatetrack.length == 1 && fordatetrack[0].track.length == 1) {

                            // merge the track data

                              //logger.info("data specific track exists",mytrack[0].track[0]);

                            type1tracks = fordatetrack[0].track[0].tracked[0].tracked;
                            type2tracks = fordatetrack[0].track[0].tracked[1].tracked;
                            type3tracks = fordatetrack[0].track[0].tracked[2].tracked;
                            type4tracks = fordatetrack[0].track[0].tracked[3].tracked;
                            type5tracks = fordatetrack[0].track[0].tracked[4].tracked;
                            type6tracks = fordatetrack[0].track[0].tracked[5].tracked;
                            //logger.info("type5tracks",type5tracks);



                        }


                        sadhanasregs[0].sadhanas = _.each(sadhanasregs[0].sadhanas, function(obj) {
                            //logger.info("next sadhana"+JSON.stringify(obj));
                            if (_.contains(type1tracks, obj.id)) {
                                return _.extend(obj, {
                                    "checked": true
                                });

                            } else {
                                return _.extend(obj, {
                                    "checked": false
                                });
                            }

                        });
                        if (sadhanasregs.length > 1)
                            sadhanasregs[1].sadhanas = _.each(sadhanasregs[1].sadhanas, function(obj) {
                                //logger.info("next sadhana"+JSON.stringify(obj));
                                if (_.contains(type2tracks, obj.id)) {
                                    return _.extend(obj, {
                                        "checked": true
                                    });

                                } else {
                                    return _.extend(obj, {
                                        "checked": false
                                    });
                                }

                            });
                        if (sadhanasregs.length > 2)
                            sadhanasregs[2].sadhanas = _.each(sadhanasregs[2].sadhanas, function(obj) {
                                //logger.info("next sadhana"+JSON.stringify(obj));
                                if (_.contains(type3tracks, obj.id)) {
                                    return _.extend(obj, {
                                        "checked": true
                                    });

                                } else {
                                    return _.extend(obj, {
                                        "checked": false
                                    });
                                }

                            });
                        if (sadhanasregs.length > 3)
                            sadhanasregs[3].sadhanas = _.each(sadhanasregs[3].sadhanas, function(obj) {
                                //logger.info("next sadhana"+JSON.stringify(obj));
                                if (_.contains(type4tracks, obj.id)) {
                                    return _.extend(obj, {
                                        "checked": true
                                    });

                                } else {
                                    return _.extend(obj, {
                                        "checked": false
                                    });
                                }

                            });

                        if (sadhanasregs.length > 4){

                           // logger.info(" ---> common values tracked found")
                            sadhanasregs[4].values = _.each(sadhanasregs[4].values, function(obj) {
                             //   logger.info("next value"+JSON.stringify(obj));
                               // logger.info("type5 track",type5tracks);
                            	 var item = _.find(type5tracks, function(track){
                                 //   logger.info("next type5track ",track);
                            		return track.id === obj.id;
                            	});
                                // logger.info("next item" ,item);
                                if (!_.isUndefined(item)) {
                                    return _.extend(obj, {
                                        "checked": true,
                                        "scale": item.scale
                                    });

                                } else {
                                    return _.extend(obj, {
                                        "checked": false
                                    });
                                }

                            });
                        }


  //custom values
                      if (sadhanasregs.length > 5){

                           // logger.info(" ---> common values tracked found")
                            sadhanasregs[5].values = _.each(sadhanasregs[5].values, function(obj) {
                             //   logger.info("next value"+JSON.stringify(obj));
                               // logger.info("type5 track",type5tracks);
                                 var item = _.find(type6tracks, function(track){
                                 //   logger.info("next type5track ",track);
                                    return track.id === obj.id;
                                });
                                // logger.info("next item" ,item);
                                if (!_.isUndefined(item)) {
                                    return _.extend(obj, {
                                        "checked": true,
                                        "scale": item.scale
                                    });

                                } else {
                                    return _.extend(obj, {
                                        "checked": false
                                    });
                                }

                            });
                        }



                        res.end(JSON.stringify(sadhanasregs));
                         logger.info("           } \n");

                    });

                //}
            });
    },

    //  update individual track updates from mobile app
    //  post /sadhanas/:userid/:fordate
    // sample payload [{"type":1,"tracked":[{"id":5,"checked":true}]}]
    sadhanasTracked: function(req, res) {



        var fordateRequest = req.params.fordate;
        var fordate = moment().format('YYYY-MM-DD');

        if (fordateRequest == null || fordateRequest == "null" || fordateRequest.length < 1) {
            fordateRequest = fordate;
        }
        //console.log('++++++++:::::id',req.params.userid," sadhanlist" ,req.body);

        logger.info('++++++++++++++++ {sadhanasTracked - \n userid', req.params.userid, " sadhanlist : ", "fordateRequest :", fordateRequest," payload :",JSON.stringify(req.body));

        // update process
        // 1. read curent tracking.
        req.db.collection('sadhakas').find({
            "userid": parseInt(req.params.userid),
            "track.fordate": fordateRequest
        }, {
            "id": 1,
            "track.fordate.$": 1
        }).limit(1).toArray(function(err, mytrack) {




            var newmySadhanasTracked = [{
                    type: 1,
                    tracked: []
                }, {
                    type: 2,
                    tracked: []
                }, {
                    type: 3,
                    tracked: []
                }, {
                    type: 4,
                    tracked: []
                },{
                    type: 5,
                    tracked: []
                },{
                    type: 6,
                    tracked: []
                }


            ];


            var type1tracks = [];
            var type2tracks = [];
            var type3tracks = [];
            var type4tracks = [];
            var type5tracks = [];

            if (mytrack.length == 1 && mytrack[0].track.length == 1) {

                // merge the track data

                //   logger.info("data specific track exists",mytrack[0].track[0]);

                type1tracks = mytrack[0].track[0].tracked[0].tracked;
                type2tracks = mytrack[0].track[0].tracked[1].tracked;
                type3tracks = mytrack[0].track[0].tracked[2].tracked;
                type4tracks = mytrack[0].track[0].tracked[3].tracked;
                type5tracks = mytrack[0].track[0].tracked[4].tracked;
                type6tracks = mytrack[0].track[0].tracked[5].tracked;
                newmySadhanasTracked[0].tracked = type1tracks;
                newmySadhanasTracked[1].tracked = type2tracks;
                newmySadhanasTracked[2].tracked = type3tracks;
                newmySadhanasTracked[3].tracked = type4tracks;
                newmySadhanasTracked[4].tracked = type5tracks;
                newmySadhanasTracked[5].tracked = type6tracks;

                //logger.info("type1tracks",type1tracks);



            } else {
                logger.info("data specific track does not exists");

                // insert the new row..
            }
            //logger.info("Track for the userid is ",mytrack[0]);

            // 2. read screen changes.


            // 3. merge tracks . remove omits/add new.
            // 4. pull curent tracks
            // 5. push new track .
            //[{"type":1,"tracked":[{"id":5,"checked":true}]}]


            //logger.info(JSON.stringify(req.body[0]));


            var sadhanatype = req.body[0].type;
            if (sadhanatype == 1) {

                _.each(req.body[0].tracked,
                    function(obj) {
                        //logger.info("obj", obj);

                        if (obj.checked) {
                            type1tracks.push(parseInt(obj.id));

                        } else {
                            //      logger.info("checked false");
                            //    logger.info(JSON.stringify(type1tracks));
                            type1tracks = _.reject(type1tracks,
                                function(rejid) {
                                    //   logger.info("next rej id",rejid);
                                    return obj.id == rejid;
                                });
                            //  logger.info(JSON.stringify(type1tracks));
                        }
                    });
                newmySadhanasTracked[0].tracked = type1tracks;
            } else if (sadhanatype == 2) {

                _.each(req.body[0].tracked,
                    function(obj) {
                        if (obj.checked) {
                            type2tracks.push(parseInt(obj.id));

                        } else {
                            type2tracks = _.reject(type2tracks,
                                function(rejid) {
                                    return obj.id == rejid;
                                });
                        }
                    });
                newmySadhanasTracked[1].tracked = type2tracks;
            } else if (sadhanatype == 3) {

                _.each(req.body[0].tracked,
                    function(obj) {
                        if (obj.checked) {
                            type3tracks.push(parseInt(obj.id));

                        } else {
                            type3tracks = _.reject(type3tracks,
                                function(rejid) {
                                    return obj.id == rejid;
                                });
                        }
                    });
                newmySadhanasTracked[2].tracked = type3tracks;
            } else if (sadhanatype == 4) {

                _.each(req.body[0].tracked,
                    function(obj) {
                        if (obj.checked) {
                            type4tracks.push(parseInt(obj.id));

                        } else {
                            type4tracks = _.reject(type4tracks,
                                function(rejid) {
                                    return obj.id == rejid;
                                });
                        }
                    });
                newmySadhanasTracked[3].tracked = type4tracks;
            } else if (sadhanatype == 5) {

                _.each(req.body[0].tracked,
                    function(obj) {
                          // first remove if exist
                        type5tracks = _.reject(type5tracks,
                        function(rejobj) {

                                    return obj.id == rejobj.id;
                        });

                        logger.info("after filtering out",JSON.stringify(type5tracks));

                        //push with new values

                        var newType5track={"id":parseInt(obj.id),"scale":parseInt(obj.scale)};
                            type5tracks.push(newType5track);

                    });
                newmySadhanasTracked[4].tracked = type5tracks;
            } else if (sadhanatype == 6) {

                _.each(req.body[0].tracked,
                    function(obj) {
                          // first remove if exist
                        type6tracks = _.reject(type6tracks,
                        function(rejobj) {

                                    return obj.id == rejobj.id;
                        });

                        logger.info("after filtering out",JSON.stringify(type6tracks));

                        //push with new values

                        var newType6track={"id":parseInt(obj.id),"scale":parseInt(obj.scale)};
                            type6tracks.push(newType6track);

                    });
                newmySadhanasTracked[5].tracked = type6tracks;
            }




            //console.log("mySadhanasTracked", newmySadhanasTracked);
            // console.log("mySadhanasTracked" ,_.pluck(mySadhanasTracked,'id'));



            // first remove current day record
            req.db.collection('sadhakas').update({
                    "userid": parseInt(req.params.userid)
                }, {
                    $pull: {
                        "track": {
                            "fordate": fordateRequest
                        }
                    }
                },
                function(err, data) {
                    if (err) logger.error(err);
                    else {
                    //    logger.info('********* updated for date  ( ' + fordateRequest + ' ) record removed *******');
                        // add a notification
                    }

                    var todaysupdate = {
                        "fordate": fordateRequest,
                        "tracked": newmySadhanasTracked
                    };

                    req.db.collection('sadhakas').update({
                            "userid": parseInt(req.params.userid)
                        }, {
                            $push: {
                                "track": todaysupdate
                            }
                        }, {
                            new: true
                        },
                        function(err, data) {
                            if (err) logger.error(err);
                            else {
                                logger.info('********* fordate - ' + fordate + ' ) record updated *******');
                                // add a notification
                            }



                            res.end('{"status" : "good"}');
                             logger.info("           } \n");
                        });



                    res.end('{"status" : "good"}');

                      logger.info("           } \n");
                });


        });

    },
    
    //For a given userid register/update sadhanas/practices
    // post /sadhanaregistration
    // put /sadhanaupdate
    sadhanaUpdate: function(req, res) {
        logger.info('{ sadhanaUpdate - \n Invoked Sadhana Registration/Update  for userid',parseInt(req.body.userid));
        //logger.info("payload -------------> ",req.body);

        utils.getSadhanas(function(err, commonsadhanas) {
            var keys = req.body.fields.common_list;


            //_.each(commonsadhanas, function(obj1){logger.info("next sadhana"+JSON.stringify(obj1));});
            var usercommonsadhanas = _.each(commonsadhanas.sadhanaslist.sadhanas, function(obj) {

                //logger.info("next sadhana"+JSON.stringify(obj));

                if (_.contains(keys, obj.id)) {

                    // logger.info("obj.id "+ obj.id + " true");

                    return _.extend(obj, {
                        "selected": true
                    });

                } else {

                    //logger.info("obj.id "+ obj.id + " false");

                    return _.extend(obj, {
                        "selected": false
                    });

                }

            });


           
            var valueskeys = req.body.fields.common_value_list;
            //	logger.info("Request body", JSON.stringify(req.body));
            //	logger.info("adding commonvalues for a new user", valueskeys);
            var common_values = _.each(commonsadhanas.valueslist.values, function(obj) {

                //	logger.info("next sadhana"+JSON.stringify(obj));

                if (_.contains(valueskeys, obj.id)) {

                     //	logger.info("obj.id "+ obj.id + " true");

                    return _.extend(obj, {
                        "selected": true
                    });

                } else {

                    //	logger.info("obj.id "+ obj.id + " false");

                    return _.extend(obj, {
                        "selected": false
                    });

                }

            });

            //logger.info("usercommonvalues ",JSON.stringify(common_values));


            // [ { "text": "sairam custom practice 1",       "description": ""},{"text": "sairam custom practice 2","description": ""}]
            var custom_sadhanas_id = 1;
            var custom_sadhanas = _.map(req.body.fields.custom_list.text,
                function(obj, key) {
                    // console.log("obj", obj);

                    var csadhana = {
                        "id": custom_sadhanas_id,
                        "text": utils.encrypt(obj),
                        "description": ""
                    };
                    custom_sadhanas_id = custom_sadhanas_id + 1;
                    return csadhana;
                });


          //  logger.info("custom_Sadhanas" + JSON.stringify(custom_sadhanas));

            
            var custom_value_id = 1;
            var custom_values = _.map(req.body.fields.custom_value_list.text,
                function(obj, key) {
                    // console.log("obj", obj);

                    var cvalues = {
                        "id": custom_value_id,
                        "text": utils.encrypt(obj),
                        "description": "",
                        "scale": 3,
                        "checked": false,
                     };
                    custom_value_id = custom_value_id + 1;
                    return cvalues;
                });


          //  logger.info("custom_Sadhanas" + JSON.stringify(custom_values));

            
            logger.info("agegroup = ",req.body.fields.group);

            var updatePayload = {
                 "settings" : {
                        "notifcations" : {
                            "allow_pushnotifications" : true,
                            "allow_email" : true
                        },
                        "SaiCenter" : {
                            "agegroup" : req.body.fields.group,
                            "center" : req.body.fields.affiliation,
                            "region" : ""
                        }
                    },
                "practicesignedup": true,
                "sadhanaregistrations": [{
                    "type": 1,
                    "sadhanas": usercommonsadhanas,
                }, {
                    "type": 2,
                    "sadhanas": [{
                        "text": utils.encrypt(req.body.fields.healthybody_goal),
                        "goal": utils.encrypt(req.body.fields.healthybody_goal),
                        "subgoal": utils.encrypt(req.body.fields.healthybody_subgoals),
                        "id": 10,
                        "img": "s1.jpg"
                    }]
                }, {
                    "type": 3,
                    "sadhanas": [{
                        "text": utils.encrypt(req.body.fields.healthymind_goal),
                        "goal": utils.encrypt(req.body.fields.healthymind_goal),
                        "subgoal": utils.encrypt(req.body.fields.healthymind_subgoals),
                        "id": 11,
                        "img": "s2.jpg"
                    }]
                }, {
                    "type": 4,
                    "sadhanas": custom_sadhanas
                },
                {
                    "type": 5,
                    "values": common_values
                },
                {
                    "type": 6,
                    "values": custom_values
                },
                ]
            };

            updateSadhakaN(req, updatePayload, function(err, data) {
                if (err) logger.error(err);
                else {
                    logger.info('Sadhanas Updated for User ' + req.body.userid);
                    // add a notification to user that sadhana has been updated
                }
                res.end('{"status" : "good"}');
                  logger.info("           } \n");
            });
        });

        
    },
    // authentication of a user from mobile app
    auth: function(req, res) {
        logger.info("{auth - \n in user auth function");
        var id = req.body.userid;
        logger.info('get user for id ', id.charAt(0)+'*******');
        var authresponse = {
            "authenticated": false
        };

        req.db.collection('sadhakas').find(
        {$or:[{user_login:req.body.userid},
              {user_email:req.body.userid}]
        }).
        toArray(function(err, docs) {
            //logger.info(docs[0].user_pass);
            if (docs.length > 0) {

                bcrypt.compare(req.body.auths, docs[0].user_pass, function(err, result) {
                    if (result == true) {

                        logger.info(id.charAt(0)+'*******' + " successfully authenticated " + result);
                        authresponse = {
                            "user_login": docs[0].user_login,
                            "user_email": docs[0].user_email,
                            "userid": docs[0].userid,
                            "id": docs[0].id,
                            "authenticated": true,
                            "practicesignedup" :docs[0].practicesignedup, 
                            "notreadytotrack":true
                        }
                        if(docs[0].practicesignedup){
                           authresponse.notreadytotrack =false; 
                        }
                        res.end(JSON.stringify(authresponse));


                    } else {

                        logger.info(req.body.userid + " not authenticated " + result);
                        authresponse = {
                            "authenticated": false,
                            "notreadytotrack":true
                        }
                        res.end(JSON.stringify(authresponse));
                         logger.info("           } \n");


                    }
                });
            } else {
                logger.info(req.body.userid + " not found in the database");
                res.end(JSON.stringify(authresponse));
                  logger.info("           } \n");

            }

              

        });
        
    },

    // create a new user after creating it in mysql..
    // post /users
/*
{
    "user_login" : "jagshetty",
    "first_name" : "Jagdeesh",
    "last_name" : "Shetty",
    "user_pass" : "testing",
    "nickname" : "Jagdeesh Shetty",
    "user_email" : "shettyjm@gmail.com",
    "role" : "subscriber",
    "userid" : 114
    
}
*/
    create: function(req, res) {
        var newuser = req.body;
        logger.info('{ create - \n userdata from client');//JSON.stringify(newuser));

       // newuser.user_pass = passwordHash.generate(newuser.user_pass);

        

        bcrypt.hash(newuser.user_pass,null,null, 
            function(err, password) {

          //if(err) logger.info(err);  

          logger.info("phash created",password);

            // Load password hash from DB
            //   bcrypt.compare("my password", hash, function(err, res) {
            // res === true
            //  });
            //  bcrypt.compare("not my password", hash, function(err, res) {
            // res === false
            //   });


            // Store hash in your password DB.
            newuser.user_pass = password;
            

            logger.info('userdata from client', JSON.stringify(newuser));
            var hrstart = process.hrtime();
            utils.getNextSequence(req.db, 'userid', function(err, obj) {
                var startt = +new Date();
                     utils.reportExecTime(hrstart,"generated next sadhakas id ",true);         
                     
                if (err) logger.info(err);
                else {
                    
                    logger.info('newuser my sql userid', newuser.userid);
                    newuser.id = obj.seq;
                    logger.info('newuser id', newuser.id);
                    newuser.created_on = new Date();
                    newuser.settings = {
                        "notifcations": {
                            "allow_pushnotifications": true,
                            "allow_email": true
                        },
                        "SaiCenter": {
                            "agegroup": "",
                            "center": "",
                            "region": ""
                        }
                    };

                    newuser.track = [];
                    utils.getSadhanas(function(err, commonsadhanas) {
var endt = +new Date();
                      console.log("utils.getSadhanas copmlete at ",endt);
                      //utils.reportExecTime(hrstart,"new sadhaka saved in ",true);         

                        //_.each(commonsadhanas, function(obj1){logger.info("next sadhana"+JSON.stringify(obj1));});
                        var usercommonsadhanas = _.each(commonsadhanas.sadhanaslist.sadhanas, function(obj) {
                            return _.extend(obj, {
                                "selected": true
                            })
                        });
                        var usercommonvalues = _.each(commonsadhanas.valueslist.values, function(obj) {
                            return _.extend(obj, {
                                "selected": true
                            })
                        });
                        newuser.practicesignedup = false;
                        newuser.sadhanaregistrations = [{
                            "type": 1,
                            "sadhanas": usercommonsadhanas,
                        },{
                            "type": 5,
                            "values": usercommonvalues,
                        }];



                        req.db.collection('sadhakas').save(newuser,
                            function(err, data) {

                               // endt = +new Date();
                              // console.log("created  new sadhaka at  ",endt);
                                    utils.reportExecTime(hrstart,"new sadhaka saved in  ",true);         


                                var response = {
                                    "userid": newuser.userid
                                }

                                res.end(JSON.stringify(response));
                                 logger.info("           } \n");
                            });
                    });
                }
            });
        });
    },
    pchange: function(req, res) {
        var changeuser = req.body;
        logger.info('{ pchange - \n userdata from client');//, JSON.stringify(newuser));
        bcrypt.hash(changeuser.user_pass, function(err, password) {



            // Load password hash from DB
            //   bcrypt.compare("my password", hash, function(err, res) {
            // res === true
            //  });
            //  bcrypt.compare("not my password", hash, function(err, res) {
            // res === false
            //   });


            // Store hash in your password DB.
            
        logger.info(changeuser.password);

            //logger.info('userdata from client', JSON.stringify(newuser));
         req.db.collection('sadhakas').update({
                            "userid": parseInt(req.params.userid)
                        }, {
                            $set: {"user_pass":password}
                        }, {
                            new: false
                        },  function(err, data) {

                                var response = {
                                    "userid": newuser.userid
                                }

                                res.end(JSON.stringify(response));
                                 logger.info("           } \n");
                            });
            });
            
    },
    // Check if the user signed up for practices or not..
    // get - /signedup/:userid
    signedup: function(req, res) {
        logger.info("{ signedup - \n Invoked signedup for user ", req.params.userid);


        //req.db.collection('sadhanas').find({"hbsadhanas.userid": parseInt(req.body.userid)}).limit(10).toArray(function(err,data){
        req.db.collection('sadhakas').find({
            "userid": parseInt(req.params.userid)
        }, {
            "practicesignedup": 1
        }).toArray(function(err, data) {

            logger.info("signedup value for this  user  is", data[0].practicesignedup);

            res.end(JSON.stringify('{ "status":' + data[0].practicesignedup + '}'));
              logger.info("           } \n");

        });

    },

    // get sadhanas for a user ..
    // get /sadhanalist/:userid
    sadhanalist: function(req, res) {
        //"userid": parseInt(req.body.userid)
        logger.info("{ sadhanalist - \n Invoked sadhanalist for user ", req.params.userid);
        sadhakaSadhanaList(req, function(data){ 
                // logger.info('data ----+' ,data[0]);
                var sadhanas = data[0].sadhanaregistrations; // The 9 common sadhanas
                //logger.info(req.query.sadhana_type_id);
               // res.end(JSON.stringify(sadhanas));

               res.end(JSON.stringify(data[0]));
               logger.info("           } \n");
            
          });
    },
    // create a question/feedback from mobile app..

    //  post /questions/:userid
    createQuestion: function(req, res) {
        logger.info(" { createQuestion - \n Create question for user ", req.params.userid);

        var question = req.body;
        logger.info("Question Payload", question);

        //generating sequence for question id
        utils.getNextSequence(req.db, 'questionid', function(err, obj) {
            if (err) logger.info(err);
            else {
                question.id = obj.seq;
                question.userid = parseInt(req.params.userid);
                //question.name = req.body.name;
                question.user_email = req.body.user_email;

               // logger.info("new Question : ", question);

                //saving the question object to the collections
                req.db.collection('questions').save(question,
                    function(err, data) {

                        var response = {
                            "userid": req.params.userid
                        }

                        res.end(JSON.stringify(response));
                        pushutils.sendSparkMail(question);
                         logger.info("           } \n");
                    });
            }
        });
    },

    // get all users count
    // get - /totalSadhakas
    progressCount: function(req, res) {
        logger.info("{ progressCount - \n Invoked progress counter API ");

        //req.db.collection('sadhanas').find({"hbsadhanas.userid": parseInt(req.params.userid)}).count(function(err,data){

        req.db.collection('sadhakas').find({}).count(function(err, data) {

            logger.info("Invoked sadhakas count ", data);
            if (data > 0)
                res.end(JSON.stringify({
                    "sadhakas": data
                }));
            else {
                logger.info("sadhakas count is zero", data);
                res.end(JSON.stringify('{ "status":false}'));
                 logger.info("           } \n");
            }
        })
    }
    /*
        {"userid":58,"fields":
        {"common_list":["1","2","4","5","6","7"],
          "custom_list":["sairam custom practice 1",
          "sairam custom practice 2"],
          "healthybody_goal":"HB Goal Register",
          "healthybody_subgoals":"<p>sairam test desc</p>\r\n","healthymind_goal":"HM Goal register","healthymind_subgoals":"<p>test desc</p>\r\n","affiliation":"Elk Grove","group":"YA"}}
        */
    
    //This is the function that sends a user's sadhana registration to MongoDB

    /*
    Message format Invoked Sadhana Registration {"userid":58,"fields":{"common_list":["1","2","3"],"custom_list":"","healthybody_goal":"test HB","healthybody_subgoals":"<p>test HB&nbsp;desc</p>\r\n","healthymind_goal":"Health Mind","healthymind_subgoals":"<p>test HM desc</p>\r\n","affiliation":"Fremont","group":"Adult"}}
    */







};


//db.sadhakas.update(    {id:2},    { $push: { track: { {"fordate" : "2015-03-10",  "tracked" : [  2,  6  ] } } }})

// utility function to update sadhaka

function updateSadhakaN(req, updatepayload, callback) {
    logger.info('{ updateSadhakaN -\n update Sadhaka with data '); //,updatepayload);
    // = db.collection('counters');  
    req.db.collection('sadhakas').update({
            "userid": parseInt(req.body.userid)
        }, {
            $set: updatepayload,

        }, {
            new: true
        },
        callback);
      logger.info("           } \n");
};


// uitility function to get sadhakas sadhanas..

function sadhakaSadhanaList(req, callback) {

    logger.info("{ sadhakaSadhanaList - \n Invoked sadhakaSadhanaList for user ", req.params.userid);
        req.db.collection('sadhakas').find({
            "userid": parseInt(req.params.userid)
        }, {
            sadhanaregistrations: 1,
            "settings.SaiCenter":1
        }).limit(1).toArray(function(err, data) {
            if (err) logger.error(err);
            else {

              //logger.info("hmsadahna encrypted text is",JSON.stringify(data[0]));

                 var hmsadhana=_.findWhere(data[0].sadhanaregistrations, {
                            "type": 2
                        });
                 if(hmsadhana !=null){

                    //logger.info("hmsadahna encrypted text is",JSON.stringify(hmsadhana));
                    hmsadhana.sadhanas[0].text=utils.decrypt(hmsadhana.sadhanas[0].text);
                    hmsadhana.sadhanas[0].goal=utils.decrypt(hmsadhana.sadhanas[0].goal);
                    hmsadhana.sadhanas[0].subgoal=utils.decrypt(hmsadhana.sadhanas[0].subgoal);
                    data[0].sadhanaregistrations[1]=hmsadhana;
                 }

                 var hbsadhana=_.findWhere(data[0].sadhanaregistrations, {
                            "type": 3
                        });
                 if(hmsadhana !=null){

                    //logger.info("hmsadahna encrypted text is",JSON.stringify(hmsadhana));
                    hbsadhana.sadhanas[0].text=utils.decrypt(hbsadhana.sadhanas[0].text);
                    hbsadhana.sadhanas[0].goal=utils.decrypt(hbsadhana.sadhanas[0].goal);
                    hbsadhana.sadhanas[0].subgoal=utils.decrypt(hbsadhana.sadhanas[0].subgoal);
                    data[0].sadhanaregistrations[2]=hbsadhana;
                 }

                

     var customsadhana=_.findWhere(data[0].sadhanaregistrations, {
                            "type": 4
                        });

                 if(customsadhana !=null){

                    customsadhana.sadhanas=_.map(customsadhana.sadhanas,function(obj,key){

                        //logger.info(JSON.stringify(obj));
                      obj.text = utils.decrypt(obj.text);
                      return obj;
                    });
                    data[0].sadhanaregistrations[3]=customsadhana;
                 }

                /********  added logic to get common sadhan decription */
                /*********** ends here the logic ****/   
                 
     var customvalues=_.findWhere(data[0].sadhanaregistrations, {
                     "type": 6
                 });
         // logger.info(JSON.stringify(data[0].sadhanaregistrations)); 
          //logger.info("customvalues ",JSON.stringify(customvalues));
          if(customvalues !=null){

        	  customvalues.values=_.map(customvalues.values,function(obj,key){

                 //logger.info("before decrypt", JSON.stringify(obj));
               obj.text = utils.decrypt(obj.text);
               //logger.info("after decrypt", JSON.stringify(obj));
               return obj;
             });
             data[0].sadhanaregistrations[5]=customvalues;
          }

         /*********** ends here the logic ****/   

                callback(data);
                 logger.info("           } \n");
            }
            //res.end('{"status" : "good"}');
        });



};



module.exports = users;














/*************************************************************

     I   G   N  O   R  E

             A  L   L  

                       T H E


                       C O D E 

                               H E R E 

                                   O N W A R D 


************************************************************/




































/****

    webscreenTrackedold: function(req, res) {
        logger.info("Invoked webscreenTracked for user & data ", JSON.stringify(req.body));
        var fordateRequest = req.params.fordate;
        var fields = req.body.fields;
        //var mySadhanasTracked = req.body.fields.common_list;
        var mySadhanasTracked = _.map(req.body.fields.common_list,
            function(obj, key) {
                console.log("obj", obj);
                return parseInt(obj);
            });

        logger.info("changed newsadhanas", mySadhanasTracked);

        if (fields.hb_cbox != null && fields.hb_cbox != '') {
            logger.info("hb checked");
            mySadhanasTracked.push(10);

        }
        if (fields.hm_cbox != null && fields.hm_cbox != '') {
            logger.info("hm checked");
            mySadhanasTracked.push(11);

        }
        // first remove current day record
        req.db.collection('sadhakas').update({
                "userid": parseInt(req.params.userid)
            }, {
                $pull: {
                    "track": {
                        "fordate": fordateRequest
                    }
                }
            },
            function(err, data) {
                if (err) logger.error(err);
                else {
                    logger.info('********* updated for date  ( ' + fordateRequest + ' ) record removed *******');
                    // add a notification
                }
                var todaysupdate = {
                    "fordate": fordateRequest,
                    "tracked": mySadhanasTracked
                };

                req.db.collection('sadhakas').update({
                        "userid": parseInt(req.params.userid)
                    }, {
                        $push: {
                            "track": todaysupdate
                        }
                    }, {
                        new: true
                    },
                    function(err, data) {
                        if (err) logger.error(err);
                        else {
                            logger.info('********* todays( ' + fordate + ' ) record updated *******');
                            // add a notification
                        }



                        res.end('{"status" : "good"}');
                    });



                res.end('{"status" : "good"}');
            });


    },
    webscreenTrackCustom: function(req, res) {
        var qryfordate = req.params.fordate;
        logger.info("Invoked webscreenTrackCustom for user & data {", req.params.userid + '} { ' + req.params.fordate + '}');
        req.db.collection('sadhakas').find({
            "userid": parseInt(req.params.userid)
        }, {
            "sadhanaregistrations": 1
        }).toArray(
            function(err, usrsadhanasregs) {
                if (err) logger.info(err);
                else {
                    // logger.info('data ----+' ,data[0].sadhanas);
                    var sadhanasregs = usrsadhanasregs[0].sadhanaregistrations;
                    var commonsadhanas = sadhanasregs[0].sadhanas; // The 9 common sadhanas
                    // logger.info(req.query.sadhana_type_id);
                    // If a sadhana_type_id is passed as a query parameter then return only the 9 common, in other cases add hb and hm
                    // if (req.query.sadhana_type_id == null && req.query.sadhana_type_id != 1) {

                    // }


                    req.db.collection('sadhakas').find({
                        "userid": parseInt(req.params.userid),
                        "track.fordate": qryfordate
                    }, {
                        "id": 1,
                        "track.fordate.$": 1
                    }).limit(1).toArray(function(err, mytrack) {

                        //logger.info("Track for the userid is ",mytrack[0]);
                        sadhanasregs[0].sadhanas = _.each(sadhanasregs[0].sadhanas, function(obj) {
                            //logger.info("next sadhana"+JSON.stringify(obj));
                            return _.extend(obj, {
                                "checked": true
                            });
                        });
                        if (sadhanasregs.length > 1)
                            sadhanasregs[1].sadhanas = _.each(sadhanasregs[1].sadhanas, function(obj) {
                                //logger.info("next sadhana"+JSON.stringify(obj));
                                return _.extend(obj, {
                                    "checked": true
                                });
                            });
                        if (sadhanasregs.length > 2)
                            sadhanasregs[2].sadhanas = _.each(sadhanasregs[2].sadhanas, function(obj) {
                                //logger.info("next sadhana"+JSON.stringify(obj));
                                return _.extend(obj, {
                                    "checked": true
                                });
                            });
                        if (sadhanasregs.length > 3)
                            sadhanasregs[3].sadhanas = _.each(sadhanasregs[3].sadhanas, function(obj) {
                                //logger.info("next sadhana"+JSON.stringify(obj));
                                return _.extend(obj, {
                                    "checked": false
                                });
                            });


                        logger.info("");
                        //sadhanasregs[0].sadhanas = usercommonsadhanas; 


                        if (mytrack.length == 1 && mytrack[0].track.length == 1) {

                        }

                    
                        res.end(JSON.stringify(sadhanasregs));

                    });

                }
            });
    },

    webscreenTrack: function(req, res) {
        var qryfordate = req.params.fordate;
        logger.info("Invoked webscreenTrack for user & data {", req.params.userid + '} { ' + req.params.fordate + '}');
        req.db.collection('sadhanas').find({
            id: 1
        }).limit(10).toArray(function(err, data) {
            if (err) logger.info(err);
            else {
                // logger.info('data ----+' ,data[0].sadhanas);
                var sadhanas = data[0].sadhanas; // The 9 common sadhanas
                // logger.info(req.query.sadhana_type_id);
                // If a sadhana_type_id is passed as a query parameter then return only the 9 common, in other cases add hb and hm
                // if (req.query.sadhana_type_id == null && req.query.sadhana_type_id != 1) {
                sadhanas.push(_.findWhere(data[0].hbsadhanas, {
                    "userid": parseInt(req.params.userid)
                }));
                sadhanas.push(_.findWhere(data[0].hmsadhanas, {
                    "userid": parseInt(req.params.userid)
                }));
                // }


                req.db.collection('sadhakas').find({
                    "userid": parseInt(req.params.userid),
                    "track.fordate": qryfordate
                }, {
                    "id": 1,
                    "track.fordate.$": 1
                }).limit(1).toArray(function(err, mytrack) {

                    //logger.info("Track for the userid is ",mytrack[0]);

                    var newsadhanas = _.map(sadhanas,
                        function(obj, key) {

                            //console.log("mytrack.length , mytrack[0].track[0].length " ,mytrack.length+ ' '+mytrack[0].track.length);
                            if (mytrack.length == 1 && mytrack[0].track.length == 1 && _.contains(mytrack[0].track[0].tracked, key + 1)) {
                                return _.extend(obj, {
                                    checked: true
                                });
                                //
                                //                                     console.log('obj=',obj ," key=",key);
                            } else
                                return _.extend(obj, {
                                    checked: false
                                });
                        });
                    res.end(JSON.stringify(newsadhanas));

                });

            }
        });
    },
    webscreenTrackedCustom: function(req, res) {
        logger.info("Invoked webscreenTrackedCustom for user & data ", JSON.stringify(req.body));
        var fordateRequest = req.params.fordate;
        var fields = req.body.fields;
        //var mySadhanasTracked = req.body.fields.common_list;
        var mySadhanasTracked = _.map(req.body.fields.common_list,
            function(obj, key) {
                console.log("obj", obj);
                return parseInt(obj);
            });

        logger.info("changed newsadhanas", mySadhanasTracked);

        if (fields.hb_cbox != null && fields.hb_cbox != '') {
            logger.info("hb checked");
            mySadhanasTracked.push(10);

        }
        if (fields.hm_cbox != null && fields.hm_cbox != '') {
            logger.info("hm checked");
            mySadhanasTracked.push(11);

        }
        // first remove current day record
        req.db.collection('sadhakas').update({
                "userid": parseInt(req.params.userid)
            }, {
                $pull: {
                    "track": {
                        "fordate": fordateRequest
                    }
                }
            },
            function(err, data) {
                if (err) logger.error(err);
                else {
                    logger.info('********* updated for date  ( ' + fordateRequest + ' ) record removed *******');
                    // add a notification
                }
                var todaysupdate = {
                    "fordate": fordateRequest,
                    "tracked": mySadhanasTracked
                };

                req.db.collection('sadhakas').update({
                        "userid": parseInt(req.params.userid)
                    }, {
                        $push: {
                            "track": todaysupdate
                        }
                    }, {
                        new: true
                    },
                    function(err, data) {
                        if (err) logger.error(err);
                        else {
                            logger.info('********* todays( ' + fordate + ' ) record updated *******');
                            // add a notification
                        }



                        res.end('{"status" : "good"}');
                    });



                res.end('{"status" : "good"}');
            });


    },
    sadhanas: function(req, res) {
        var fordate = moment().format('YYYY-MM-DD');
        req.db.collection('sadhanas').find({
            id: 1
        }).limit(10).toArray(function(err, ninesadhanas) {

            if (err) logger.info(err);
            else {
                console.log(fordate);
                req.db.collection('sadhakas').find({
                        "userid": parseInt(req.params.userid),
                        "track.fordate": fordate
                    }, {
                        "id": 1,
                        "first_name": 1,
                        "track.fordate.$": 1
                    }

                ).limit(1).toArray(function(err, mytrack) {

                    if (err) logger.info(err);
                    else {
                        logger.info('mytrack ----+ ( ' + fordate + ' )', mytrack);
                        //res.end(JSON.stringify(data[0].sadhanas));

                        var mySadhanas = {
                            mysadhanaList: [],
                            "hb": {
                                "text": "",
                                "id": 10,
                                "checked": false
                            },
                            "hm": {
                                "text": "",
                                "id": 11,
                                "checked": false
                            }
                        };

                        //var hbuids = ;

                        //console.log(hbuids);
                        if (_.contains(_.pluck(ninesadhanas[0].hbsadhanas, 'userid'), parseInt(req.params.userid))) {
                            //console.log("mtching hb found") ;

                            mySadhanas.hb.text = _.findWhere(ninesadhanas[0].hbsadhanas, {
                                "userid": parseInt(req.params.userid)
                            }).text;
                        }

                        if (_.contains(_.pluck(ninesadhanas[0].hmsadhanas, 'userid'), parseInt(req.params.userid))) {
                            //console.log("mtching hb found") ;

                            mySadhanas.hm.text = _.findWhere(ninesadhanas[0].hmsadhanas, {
                                "userid": parseInt(req.params.userid)
                            }).text;
                        }



                        mySadhanas.mysadhanaList = _.map(ninesadhanas[0].sadhanas,
                            function(obj, key) {

                                //      console.log(mytrack[0].track[0]);
                                if (mytrack.length == 1 && _.contains(mytrack[0].track[0].tracked, key + 1)) {
                                    return _.extend(obj, {
                                        checked: true
                                    });
                                    //
                                    //                                     console.log('obj=',obj ," key=",key);
                                } else
                                    return _.extend(obj, {
                                        checked: false
                                    });

                            }
                        );

                        if (mytrack.length == 1 && _.contains(mytrack[0].track[0].tracked, 10)) {
                            console.log("hb already tracked");
                            mySadhanas.hb.checked = true;
                        } else if (mytrack.length == 1 && _.contains(mytrack[0].track[0].tracked, 11)) {
                            console.log("hm laready tracked");
                            mySadhanas.hm.checked = true;
                        }


                        //                      console.log("mySadhanas",mySadhanas);

                        res.end(JSON.stringify(mySadhanas));


                    }
                });



            }
        });
    },
    getAll: function(req, res) {
        logger.info("in user getall function");


        var collection = req.db.collection('users').find().limit(10).toArray(function(err, docs) {
            res.json(docs);
            //    db.close();
        });
    },

    getOne: function(req, res) {
        var id = req.params.id;
        logger.info('get user for id ', req.params.id);
        req.db.collection('users').find({
            id: parseInt(req.params.id)
        }).
        toArray(function(err, docs) {
            res.json(docs);

        });
    },

    delete: function(req, res) {
        res.json(true);
    },
    sadhanaRegCustom: function(req, res) {
        logger.info('Invoked Sadhana Registration', JSON.stringify(req.body));
        var bodyJSON = req.body; // The json from the php layer
        var uid = req.body.userid; // Reading the userid from the json
        logger.info('userid from the php layer for sadhanaregistration:', uid); // logging the user id
        //A variable which I will populate with the sadhanas registered for

        utils.getNextSequence(req.db, 'sadhanaregid', function(err, obj) {
            if (err) logger.info(err);
            else {
                // logger.info('sadhanaregid', obj);
                logger.info('sadhanaregid', obj.seq);
                // logger.info('req.body.fields.common_list' ,req.body.fields.common_list);
                //utils.getSadhanas(function(err, commonsadhanas){
                //Query to update the sadhaka's sadhanas
                utils.getSadhanas(function(err, commonsadhanas) {


                    var keys = req.body.fields.common_list;
                    var usercommonsadhanas = _.map(keys, function(key, obj) {
                        // var sadhanasreg= _.findWhere(json.sadhanas,{"id":key});
                        //  console.log("sadhanasreg :"+JSON.stringify(sadhanasreg));
                        return _.omit(_.findWhere(commonsadhanas.sadhanas, {
                            "id": key
                        }), "type");

                    });
                    // console.log(JSON.stringify(matching));

                    var custom_sadhans=_.map(req.body.fields.custom_list, function(key, obj) {
                        // var sadhanasreg= _.findWhere(json.sadhanas,{"id":key});
                        //  console.log("sadhanasreg :"+JSON.stringify(sadhanasreg));
                        obj.text=encrypt(obj.text);
                        obj.description=encrypt(obj.description);
                        return obj;

                    });

                    var sadhanaReg = {
                        "id": obj.seq,
                        "userid": parseInt(req.body.userid),
                        "sadhanaregistrations": [{
                            "type": 1,
                            "sadhanas": usercommonsadhanas,
                        }, {
                            "type": 2,
                            "sadhanas": [{
                                "text": encrypt(req.body.fields.healthybody_goal),
                                "goal": encrypt(req.body.fields.healthybody_goal),
                                "subgoal": encrypt(req.body.fields.healthybody_subgoals),
                                "id": 10,
                                "img": "s1.jpg"
                            }]
                        }, {
                            "type": 3,
                            "sadhanas": [{
                                "text": encrypt(req.body.fields.healthymind_goal),
                                "goal": encrypt(req.body.fields.healthymind_goal),
                                "subgoal": encrypt(req.body.fields.healthymind_subgoals),
                                "id": 11,
                                "img": "s2.jpg"
                            }]
                        }, {
                            "type": 4,
                            "sadhanas": req.body.fields.custom_sadhans
                        }]
                    };





                    req.db.collection('sadhakas').update({
                        "userid": parseInt(req.body.userid)
                    }, {
                        $set: {
                            "sadhanaregistrations": sadhanaReg.sadhanaregistrations
                        },

                    }, {
                        new: true
                    }, function(err, data) {

                        var response = {
                            "status": "ok"
                        }

                        res.end(JSON.stringify(response));
                    });
                });

            }

        });
        //updateSadhaka(req);


    },
        sadhanaReg: function(req, res) {
        logger.info('Invoked Sadhana Registration', JSON.stringify(req.body));
        var bodyJSON = req.body; // The json from the php layer
        var uid = req.body.userid; // Reading the userid from the json
        logger.info('userid from the php layer for sadhanaregistration:', uid); // logging the user id
        //A variable which I will populate with the sadhanas registered for

        var myhbsadhana = {
            "type": 2,
            "text": req.body.fields.healthybody_goal,
            "goal": req.body.fields.healthybody_goal,
            "subgoal": req.body.fields.healthybody_subgoals,
            "userid": parseInt(req.body.userid),
            "id": 10,
            "img": "s1.jpg"
        };
        var myhmsadhana = {
            "type": 3,
            "text": req.body.fields.healthymind_goal,
            "goal": req.body.fields.healthymind_goal,
            "subgoal": req.body.fields.healthymind_subgoals,
            "userid": parseInt(req.body.userid),
            "id": 11,
            "img": "s2.jpg"
        };
        //Query to update the sadhaka's sadhanas
        req.db.collection('sadhanas').update({
                "id": 1
            }, {
                $push: {
                    "hbsadhanas": myhbsadhana,
                    "hmsadhanas": myhmsadhana
                },

            }, {
                new: true
            },
            function(err, data) {
                if (err) logger.error(err);
                else {
                    logger.info('Sadhana Registration updated for user ' + uid);
                    // add a notification to user that sadhana has been updated
                }
                res.end('{"status" : "good"}');
            });


        updateSadhaka(req);


    },

    sadhanaUpdateOld: function(req, res) {
        logger.info('Invoked Sadhana Update', JSON.stringify(req.body));
        

        //assuming that user has registered for hb and hm
        req.db.collection('sadhanas').update({
                "id": 1,
                "hbsadhanas.userid": parseInt(req.body.userid)
            }, {
                $set: {
                    "hbsadhanas.$.text": req.body.fields.healthybody_goal,
                    "hbsadhanas.$.goal": req.body.fields.healthybody_goal,
                    "hbsadhanas.$.subgoal": req.body.fields.healthybody_subgoals,
                    "hmsadhanas.$.text": req.body.fields.healthymind_goal,
                    "hmsadhanas.$.goal": req.body.fields.healthymind_goal,
                    "hmsadhanas.$.subgoal": req.body.fields.healthymind_subgoals
                },

            }, {
                new: true
            },
            function(err, data) {
                if (err) logger.error(err);
                else {
                    logger.info('Sadhanas Updated for User ' + req.body.userid);
                    // add a notification to user that sadhana has been updated
                }
                res.end('{"status" : "good"}');
            });

        updateSadhaka(req);

        //res.end(JSON.stringify(response));
    }



    function updateSadhaka(req) {
    logger.info('update Sadhaka');
    // = db.collection('counters');  
    req.db.collection('sadhakas').update({
            "userid": parseInt(req.body.userid)
        }, {
            $set: {
                "settings.SaiCenter.center": req.body.fields.affiliation,
                "settings.SaiCenter.agegroup": req.body.fields.group
            },

        }, {
            new: false
        },
        function(err, data) {
            if (err) logger.error(err);
            else {
                logger.info('Sadhakas center affiliation Registration updated for user ' + req.body.userid);
                // add a notification to user that sadhana has been updated
            }
            //res.end('{"status" : "good"}');
        });
};



function sadhakaSadhanaListDelelte(req, callback) {

    var fordate = moment().format('YYYY-MM-DD');
    var qrydate = (req.query.fordate != null) ? req.query.fordate : fordate;
    console.log("In sadhakaSadhanaList qrydate", qrydate);
    req.db.collection('sadhakas').find({
        "userid": parseInt(req.params.userid),
        "track.fordate": qrydate
    }, {
        "id": 1,
        "first_name": 1,
        "track.fordate.$": 1
    }).toArray(callback);



};


//var fordate = moment().format('YYYY-MM-DD');
****/