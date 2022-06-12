/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/coreui/media-cropp.js":
/*!********************************************!*\
  !*** ./resources/js/coreui/media-cropp.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var self = this;
this.changePort = ''; // :8000

this.removeFolderModal = new coreui.Modal(document.getElementById('cropp-img-modal'));
this.cropper = null;
this.croppUrl = null;
this.croppFileId = null;

this.uploadCroppedImage = function () {
  self.cropper.getCroppedCanvas().toBlob(function (blob) {
    var formData = new FormData();
    formData.append('file', blob);
    formData.append('thisFolder', document.getElementById('this-folder-id').value);
    formData.append('id', self.croppFileId);
    axios.post('/media/file/cropp', formData).then(function (response) {
      location.reload();
    })["catch"](function (error) {
      console.log(error);
    });
  }
  /*, 'image/png' */
  );
};

this.afterShowedCroppModal = function () {
  if (self.cropper !== null) {
    self.cropper.replace(self.croppUrl);
  } else {
    var image = document.getElementById('cropp-img-img');
    self.cropper = new Cropper(image, {
      minContainerWidth: 600,
      minContainerHeight: 600
    });
  }
};

this.showCroppModal = function (data) {
  self.croppUrl = data.url;
  self.croppUrl = self.croppUrl.replace('localhost', 'localhost' + self.changePort);
  document.getElementById('cropp-img-img').setAttribute('src', self.croppUrl);
  self.removeFolderModal.show();
};

this.croppImg = function (e) {
  self.croppFileId = e.target.getAttribute('atr');
  axios.get('/media/file?id=' + self.croppFileId + '&thisFolder=' + document.getElementById('this-folder-id').value).then(function (response) {
    self.showCroppModal(response.data);
  })["catch"](function (error) {
    console.log(error);
  });
};

var croppFiles = document.getElementsByClassName("file-cropp-file");

for (var i = 0; i < croppFiles.length; i++) {
  croppFiles[i].addEventListener('click', this.croppImg);
}

document.getElementById("cropp-img-modal").addEventListener("show.coreui.modal", this.afterShowedCroppModal);
document.getElementById('cropp-img-save-button').addEventListener('click', this.uploadCroppedImage);

/***/ }),

/***/ 3:
/*!**************************************************!*\
  !*** multi ./resources/js/coreui/media-cropp.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Users\Piety\Downloads\coreui-free-laravel-admin-template-master\resources\js\coreui\media-cropp.js */"./resources/js/coreui/media-cropp.js");


/***/ })

/******/ });