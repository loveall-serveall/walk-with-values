var logger = require("../init/logger");

var utils = {
  validateuser: function(req, res) {
    res.json(true);
  }
};

var data = [{
  name: 'product 1',
  id: '1'
}, {
  name: 'product 2',
  id: '2'
}, {
  name: 'product 3',
  id: '3'
}];

module.exports = utils;
