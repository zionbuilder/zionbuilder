!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.ZBVideoBg=t():e.ZBVideoBg=t()}(window,(function(){return function(e){var t={};function o(i){if(t[i])return t[i].exports;var n=t[i]={i:i,l:!1,exports:{}};return e[i].call(n.exports,n,n.exports,o),n.l=!0,n.exports}return o.m=e,o.c=t,o.d=function(e,t,i){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(o.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)o.d(i,n,function(t){return e[t]}.bind(null,n));return i},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="dist/",o(o.s=1)}([,function(e,t,o){"use strict";o.r(t),o.d(t,"default",(function(){return n}));let i=0;class n{constructor(e,t={}){this.options={autoplay:!0,muted:!0,loop:!0,local_sources:{},controlsPosition:"bottom-left",controls:t.controls||!0,videoSource:"local",responsive:!1,...t},this.domNode=e,this.videoIndex=i++,this.domNode.classList.add("hg-video-bg__wrappper");const o=document.createElement("div");o.className="hg-video-bg__container",o.id="hg-video-bg--"+this.videoIndex,this.videoInstance=new ZBVideo(o,{...this.options,controls:!1}),this.videoInstance.on("video_ready",this.onVideoReady.bind(this)),this.videoContainer=this.domNode.appendChild(o)}onVideoReady(){this.options.autoplay&&(this.playing=!0,this.domNode.classList.add("hg-video-bg--playing")),this.options.muted&&(this.muted=!0,this.domNode.classList.add("hg-video-bg--muted")),this.options.controls&&(this.controlsWrapper=this.getControlsHTML(),this.domNode.appendChild(this.controlsWrapper)),this.onResizeCallback=this.onWindowResize.bind(this),"local"!==this.videoSource&&this.initResizer()}initResizer(){window.addEventListener("resize",this.onResizeCallback),this.onWindowResize()}onWindowResize(){const{width:e,height:t}=this.domNode.getBoundingClientRect();let o,i;e===t?(o=1.78*e,i=1.78*t):e<t?(o=1.78*t,i=t):(o=e,i=1.78*e);const n=this.videoInstance.getVideoContainer();n.style.width=o+"px",n.style.height=i+"px"}getControlsHTML(){const e=document.createElement("div");e.className="hg-video-bg__controls",e.dataset.position=this.options.controlsPosition;const t=document.createElement("span");t.className="hg-video-bg__controls-button hg-video-bg__controls-button--play";const o=document.createElement("span");return o.className="hg-video-bg__controls-button hg-video-bg__controls-button--mute",e.appendChild(t),e.appendChild(o),o.addEventListener("click",this.toggleMute.bind(this)),t.addEventListener("click",this.togglePlay.bind(this)),e}play(){this.videoInstance.play(),this.playing=!0,this.domNode.classList.add("hg-video-bg--playing")}pause(){this.videoInstance.pause(),this.playing=!1,this.domNode.classList.remove("hg-video-bg--playing")}togglePlay(){this.playing?this.pause():this.play()}mute(){this.videoInstance.mute(),this.muted=!0,this.domNode.classList.add("hg-video-bg--muted")}unMute(){this.videoInstance.unMute(),this.muted=!1,this.domNode.classList.remove("hg-video-bg--muted")}toggleMute(){this.muted?this.unMute():this.mute()}destroy(){for(this.videoInstance=null;this.domNode.firstChild;)this.domNode.removeChild(this.domNode.firstChild);window.removeEventListener("resize",this.onResizeCallback)}}jQuery("document").ready((function(){jQuery(".zbjs_video_background").each((e,t)=>{const o=jQuery(t),i=t.dataset.zionVideoBackground,s=JSON.parse(i);new n(t,s),o.zionVideoBackgroundConfig=i})}))}]).default}));