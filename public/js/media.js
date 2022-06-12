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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/coreui/media.js":
/*!**************************************!*\
  !*** ./resources/js/coreui/media.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var self = this;
this.removeFolderModal = new coreui.Modal(document.getElementById('remove-folder-modal'));
this.removeFileModal = new coreui.Modal(document.getElementById('remove-file-modal'));

this.showCard = function (showThisCard) {
  document.getElementById('file-rename-file-card').classList.add('d-none');
  document.getElementById('file-info-card').classList.add('d-none');
  document.getElementById('file-rename-folder-card').classList.add('d-none');
  document.getElementById('file-move-file').classList.add('d-none');
  document.getElementById('file-move-folder').classList.add('d-none');
  document.getElementById(showThisCard).classList.remove('d-none');
};

this.buildFileInfoCard = function (data) {
  document.getElementById("file-div-name").innerText = data['name'];
  document.getElementById("file-div-real-name").innerText = data['realName'];
  document.getElementById("file-div-url").innerText = data['url'];
  document.getElementById("file-div-mime-type").innerText = data['mimeType'];
  document.getElementById("file-div-size").innerText = data['size'];
  document.getElementById("file-div-created-at").innerText = data['createdAt'];
  document.getElementById("file-div-updated-at").innerText = data['updatedAt'];
};

this.buildFileRenameFileCard = function (data) {
  document.getElementById("file-rename-file-id").value = data['id'];
  document.getElementById("file-rename-file-name").value = data['name'];
};

this.buildFileRenameFolderCard = function (data) {
  document.getElementById("file-rename-folder-id").value = data['id'];
  document.getElementById("file-rename-folder-name").value = data['name'];
};

this.clickFile = function (e) {
  axios.get('/media/file?id=' + e.target.getAttribute("atr") + '&thisFolder=' + document.getElementById('this-folder-id').value).then(function (response) {
    self.buildFileInfoCard(response.data);
    self.showCard('file-info-card');
  })["catch"](function (error) {
    console.log(error);
  });
};

this.fileChangeName = function (e) {
  axios.get('/media/file?id=' + e.target.getAttribute("atr") + '&thisFolder=' + document.getElementById('this-folder-id').value).then(function (response) {
    self.buildFileInfoCard(response.data); //must be

    self.buildFileRenameFileCard(response.data);
    self.showCard('file-rename-file-card');
  })["catch"](function (error) {
    console.log(error);
  });
};

this.folderChangeName = function (e) {
  axios.get('/media/folder?id=' + e.target.getAttribute("atr")).then(function (response) {
    self.buildFileRenameFolderCard(response.data);
    self.showCard('file-rename-folder-card');
  })["catch"](function (error) {
    console.log(error);
  });
};

this.moveFile = function (e) {
  document.getElementById('file-move-file-id').value = e.target.getAttribute('atr');
  self.showCard('file-move-file');
};

this.moveFolder = function (e) {
  document.getElementById('file-move-folder-id').value = e.target.getAttribute('atr');
  var radios = document.getElementsByClassName('file-move-folder-radio');

  for (var i = 0; i < radios.length; i++) {
    if (radios[i].value === e.target.getAttribute('atr')) {
      radios[i].disabled = true;
    } else {
      radios[i].disabled = false;
    }
  }

  self.showCard('file-move-folder');
};

this.deleteFolder = function (e) {
  document.getElementById('file-delete-folder-id').value = e.target.getAttribute('atr');
  self.removeFolderModal.show();
};

this.deleteFile = function (e) {
  document.getElementById('file-delete-file-id').value = e.target.getAttribute('atr');
  self.removeFileModal.show();
};

this.renameFileCancel = function () {
  self.showCard('file-info-card');
};

this.renameFolderCancel = function () {
  self.showCard('file-info-card');
};

this.moveFileCancel = function () {
  self.showCard('file-info-card');
};

this.moveFolderCancel = function () {
  self.showCard('file-info-card');
};

var files = document.getElementsByClassName("click-file");

for (var i = 0; i < files.length; i++) {
  files[i].addEventListener('click', this.clickFile);
}

var renameButtons = document.getElementsByClassName('file-change-file-name');

for (var _i = 0; _i < renameButtons.length; _i++) {
  renameButtons[_i].addEventListener('click', this.fileChangeName);
}

renameButtons = document.getElementsByClassName('file-change-folder-name');

for (var _i2 = 0; _i2 < renameButtons.length; _i2++) {
  renameButtons[_i2].addEventListener('click', this.folderChangeName);
}

var moveButtons = document.getElementsByClassName('file-move-file');

for (var _i3 = 0; _i3 < moveButtons.length; _i3++) {
  moveButtons[_i3].addEventListener('click', this.moveFile);
}

moveButtons = document.getElementsByClassName('file-move-folder');

for (var _i4 = 0; _i4 < moveButtons.length; _i4++) {
  moveButtons[_i4].addEventListener('click', this.moveFolder);
}

var deleteButtons = document.getElementsByClassName('file-delete-folder');

for (var _i5 = 0; _i5 < deleteButtons.length; _i5++) {
  deleteButtons[_i5].addEventListener('click', this.deleteFolder);
}

deleteButtons = document.getElementsByClassName('file-delete-file');

for (var _i6 = 0; _i6 < deleteButtons.length; _i6++) {
  deleteButtons[_i6].addEventListener('click', this.deleteFile);
}

document.getElementById('file-rename-file-cancel').addEventListener('click', this.renameFileCancel);
document.getElementById('file-rename-folder-cancel').addEventListener('click', this.renameFolderCancel);
document.getElementById('file-move-file-cancel').addEventListener('click', this.moveFileCancel);
document.getElementById('file-move-folder-cancel').addEventListener('click', this.moveFolderCancel);

document.getElementById('file-file-input').onchange = function () {
  document.getElementById('file-file-form').submit();
};

self.showCard('file-info-card');

/***/ }),

/***/ 2:
/*!********************************************!*\
  !*** multi ./resources/js/coreui/media.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Users\Piety\Downloads\coreui-free-laravel-admin-template-master\resources\js\coreui\media.js */"./resources/js/coreui/media.js");


/***/ })

/******/ });