/*
	krpano ThreeJS example plugin
	- use three.js inside krpano
	- with stereo-rendering and WebVR support
	- with 3d object hit-testing (onover, onout, onup, ondown, onclick) and mouse cursor handling
*/

function krpanoplugin() {
	var local = this;
	var krpano = null;
	var device = null;
	var plugin = null;

	local.registerplugin = function (krpanointerface, pluginpath, pluginobject) {
		krpano = krpanointerface;
		window.krpano = krpanointerface;
		device = krpano.device;
		plugin = pluginobject;

		if (krpano.version < "1.19") {
			krpano.trace(3, "ThreeJS plugin - too old krpano version (min. 1.19)");
			return;
		}

		if (!device.webgl) {
			// show warning
			krpano.trace(2, "ThreeJS plugin - WebGL required");
			return;
		}

		krpano.debugmode = true;
		krpano.trace(0, "ThreeJS krpano plugin");
		// load the requiered three.js scripts
		// load_scripts(["js/lib/GLTFLoader.js", "js/lib/DRACOLoader.js"], start);
		start();
	}

	local.unloadplugin = function () {
		// no unloading support at the moment
	}

	local.onresize = function (width, height) {
		return false;
	}

	function resolve_url_path(url) {
		if (url.charAt(0) != "/" && url.indexOf("://") < 0) {
			// adjust relative url path
			url = krpano.parsepath("%CURRENTXML%/" + url);
		}

		return url;
	}

	function load_scripts(urls, callback) {

		if (urls.length > 0) {
			var url = resolve_url_path(urls.splice(0, 1)[0]);
			var script = document.createElement("script");
			script.setAttribute('type', 'module');
			script.src = url;

			script.addEventListener("load", function () { load_scripts(urls, callback); });
			script.addEventListener("error", function () { krpano.trace(3, "loading file '" + url + "' failed!"); });
			document.getElementsByTagName("head")[0].appendChild(script);
		}
		else {
			// done
			callback();
		}
	}

	// helper
	var M_RAD = Math.PI / 180.0;

	// ThreeJS/krpano objects
	var renderer = null;
	var scene = null;
	var camera = null;
	var stereocamera = null;
	var camera_hittest_raycaster = null;
	var krpano_panoview = null;
	var krpano_panoview_euler = null;
	var krpano_projection = new Float32Array(16);		// krpano projection matrix
	var krpano_depthbuffer_scale = 1.0001;				// depthbuffer scaling (use ThreeJS defaults: znear=0.1, zfar=2000)
	var krpano_depthbuffer_offset = -0.2;

	function start() {
		// create the ThreeJS WebGL renderer, but use the WebGL context from krpano
		renderer = new THREE.WebGLRenderer({ canvas: krpano.webGL.canvas, context: krpano.webGL.context });
		renderer.autoClear = false;
		renderer.setPixelRatio(1);	// krpano handles the pixel ratio scaling

		renderer.shadowMap.enabled = true;
		renderer.shadowMap.type = THREE.PCFSoftShadowMap; // default THREE.PCFShadowMap

		// restore the krpano WebGL settings (for correct krpano rendering)
		restore_krpano_WebGL_state();

		// use the krpano onviewchanged event as render-frame callback (this event will be directly called after the krpano pano rendering)
		krpano.set("events[__threejs__].keep", true);
		krpano.set("events[__threejs__].onviewchange", adjust_krpano_rendering);	// correct krpano view settings before the rendering
		krpano.set("events[__threejs__].onviewchanged", render_frame);

		// enable continuous rendering (that means render every frame, not just when the view has changed)
		krpano.view.continuousupdates = true;

		// register mouse and touch events
		if (device.browser.events.mouse) {
			krpano.control.layer.addEventListener("mousedown", handle_mouse_touch_events, true);
			krpano.control.layer.addEventListener("mousemove", handle_mouse_touch_events, true);
		}
		if (device.browser.events.touch) {
			krpano.control.layer.addEventListener(device.browser.events.touchstart, handle_mouse_touch_events, true);
		}

		// basic ThreeJS objects
		scene = new THREE.Scene();
		window.scene = scene;
		camera = new THREE.Camera();
		window.camera = camera;
		stereocamera = new THREE.Camera();
		window.stereocamera = stereocamera;

		camera_hittest_raycaster = new THREE.Raycaster();
		window.camera_hittest_raycaster = camera_hittest_raycaster;
		krpano_panoview_euler = new THREE.Euler();
		window.krpano_panoview_euler = krpano_panoview_euler;

		//  perspective = new THREE.PerspectiveCamera( 45, width / height, 1, 1000 );
		// scene.add( perspective )

		// build the ThreeJS scene (start adding custom code there)
		build_scene();

		// restore the krpano WebGL settings (for correct krpano rendering)
		restore_krpano_WebGL_state();
	}

	function restore_krpano_WebGL_state() {
		var gl = krpano.webGL.context;

		gl.disable(gl.DEPTH_TEST);
		gl.cullFace(gl.FRONT);
		gl.frontFace(gl.CCW);
		gl.enable(gl.BLEND);
		gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);
		gl.activeTexture(gl.TEXTURE0);
		gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, false);
		gl.pixelStorei(gl.UNPACK_PREMULTIPLY_ALPHA_WEBGL, false);
		gl.pixelStorei(gl.UNPACK_ALIGNMENT, 4);

		// restore the current krpano WebGL program
		krpano.webGL.restoreProgram();

		//renderer.resetGLState();
		renderer.state.reset();
	}

	function restore_ThreeJS_WebGL_state() {
		var gl = krpano.webGL.context;

		gl.enable(gl.DEPTH_TEST);
		gl.depthFunc(gl.LEQUAL);
		gl.enable(gl.CULL_FACE);
		gl.cullFace(gl.BACK);
		gl.clearDepth(1);
		gl.clear(gl.DEPTH_BUFFER_BIT);

		//	renderer.resetGLState();
		renderer.state.reset();
	}

	function krpano_projection_matrix(sw, sh, zoom, xoff, yoff) {
		var m = krpano_projection;

		var pr = device.pixelratio;
		sw = pr / (sw * 0.5);
		sh = pr / (sh * 0.5);

		m[0] = zoom * sw; m[1] = 0; m[2] = 0; m[3] = 0;
		m[4] = 0; m[5] = -zoom * sh; m[6] = 0; m[7] = 0;
		m[8] = xoff; m[9] = -yoff * sh; m[10] = krpano_depthbuffer_scale; m[11] = 1;
		m[12] = 0; m[13] = 0; m[14] = krpano_depthbuffer_offset; m[15] = 1;
	}

	function update_camera_matrix(camera) {
		var m = krpano_projection;
		camera.projectionMatrix.set(m[0], m[4], m[8], m[12], m[1], m[5], m[9], m[13], m[2], m[6], m[10], m[14], m[3], m[7], m[11], m[15]);
	}

	function adjust_krpano_rendering() {
		if (krpano.view.fisheye != 0.0) {
			// disable the fisheye distortion, ThreeJS objects can't be rendered with it
			krpano.view.fisheye = 0.0;
		}
	}

	function render_frame() {
		var gl = krpano.webGL.context;
		var vr = krpano.webVR && krpano.webVR.enabled ? krpano.webVR : null;

		var sw = gl.drawingBufferWidth;
		var sh = gl.drawingBufferHeight;


		// setup WebGL for ThreeJS
		restore_ThreeJS_WebGL_state();

		// set the camera/view rotation
		krpano_panoview = krpano.view.getState(krpano_panoview);	// the 'krpano_panoview' object will be created and cached inside getState()
		krpano_panoview_euler.set(-krpano_panoview.v * M_RAD, (krpano_panoview.h - 90) * M_RAD, krpano_panoview.r * M_RAD, "YXZ");
		camera.quaternion.setFromEuler(krpano_panoview_euler);
		camera.updateMatrixWorld(true);

		// set the camera/view projection
		krpano_projection_matrix(sw, sh, krpano_panoview.z, 0, krpano_panoview.yf);
		update_camera_matrix(camera);

		// do scene updates
		update_scene();

		renderer.resetState();
		// render the scene
		if (krpano.display.stereo == false) {
			// normal rendering
			renderer.setViewport(0, 0, sw, sh);
			renderer.render(scene, camera);
		}
		else {
			// stereo / VR rendering
			sw *= 0.5;	// use half screen width

			var stereo_scale = 0.05;
			var stereo_offset = Number(krpano.display.stereooverlap);

			// use a different camera for stereo rendering to keep the normal one for hit-testing
			stereocamera.quaternion.copy(camera.quaternion);
			stereocamera.updateMatrixWorld(true);

			// render left eye
			var eye_offset = -0.03;
			krpano_projection_matrix(sw, sh, krpano_panoview.z, stereo_offset, krpano_panoview.yf);

			if (vr) {
				eye_offset = vr.eyetranslt(1);						// get the eye offset (from the WebVR API)
				vr.prjmatrix(1, krpano_projection);					// replace the projection matrix (with the one from WebVR)
				krpano_projection[10] = krpano_depthbuffer_scale;	// adjust the depthbuffer scaling
				krpano_projection[14] = krpano_depthbuffer_offset;
			}

			// add the eye offset
			krpano_projection[12] = krpano_projection[0] * -eye_offset * stereo_scale;

			update_camera_matrix(stereocamera);
			renderer.setViewport(0, 0, sw, sh);
			renderer.render(scene, stereocamera);

			// render right eye
			eye_offset = +0.03;
			krpano_projection[8] = -stereo_offset;	// mod the projection matrix (only change the stereo offset)

			if (vr) {
				eye_offset = vr.eyetranslt(2);						// get the eye offset (from the WebVR API)
				vr.prjmatrix(2, krpano_projection);					// replace the projection matrix (with the one from WebVR)
				krpano_projection[10] = krpano_depthbuffer_scale;	// adjust the depthbuffer scaling
				krpano_projection[14] = krpano_depthbuffer_offset;
			}

			// add the eye offset
			krpano_projection[12] = krpano_projection[0] * -eye_offset * stereo_scale;

			update_camera_matrix(stereocamera);
			renderer.setViewport(sw, 0, sw, sh);
			renderer.render(scene, stereocamera);
		}

		renderer.resetState();
		// important - restore the krpano WebGL state for correct krpano rendering
		restore_krpano_WebGL_state();
	}

	// -----------------------------------------------------------------------
	// ThreeJS User Content - START HERE

	window.isAnimate = false;
	var selectedObj = null;
	var isDown = false;
	var plane_point_temp = null;

	var label = null;
	label = document.createElement('div');

	function build_scene() {

		// add scene lights
		scene.add(new THREE.AmbientLight(0xE5DCDF));

		var directionalLight = new THREE.DirectionalLight(0xE5DCDF);
		directionalLight.position.x = 0.5;
		directionalLight.position.y = -1;
		directionalLight.position.z = 0;
		directionalLight.position.normalize();
		directionalLight.castShadow = true
		scene.add(directionalLight);
	}

	function do_object_hittest(mx, my) {

		const pointer = new THREE.Vector2();
		pointer.x = (mx / krpano.area.pixelwidth) * 2.0 - 1.0;
		pointer.y = -(my / krpano.area.pixelheight) * 2.0 + 1.0;

		if (krpano.display.stereo) {
			pointer.x += (pointer.x < 0.0 ? +1 : -1) * (1.0 - Number(krpano.display.stereooverlap)) * 0.5;
		}

		camera_hittest_raycaster.ray.direction.set(pointer.x, -pointer.y, 1.0).unproject(camera).normalize();
		var intersects = camera_hittest_raycaster.intersectObjects(scene.children, true);
		var i;
		var obj;

		for (i = 0; i < intersects.length; i++) {
			obj = intersects[i].object;
			if (obj && obj.properties && obj.properties.enabled) {
				return {object: obj, point: intersects[i].point};
			}
		}

		return null;
	}

	function do_object_point(mx, my) {
		const pointer = new THREE.Vector2();
		pointer.x = (mx / krpano.area.pixelwidth) * 2.0 - 1.0;
		pointer.y = -(my / krpano.area.pixelheight) * 2.0 + 1.0;

		if (krpano.display.stereo) {
			mouse_x += (mouse_x < 0.0 ? +1 : -1) * (1.0 - Number(krpano.display.stereooverlap)) * 0.5;
		}

		camera_hittest_raycaster.ray.direction.set(pointer.x, -pointer.y, 1.0).unproject(camera).normalize();
		var intersects = camera_hittest_raycaster.intersectObjects(scene.children, true);
		for (var i = 0; i < intersects.length; i++) {
			obj = intersects[i].object;
			if (obj && obj.geometry instanceof THREE.PlaneGeometry) {
				return intersects[i].point;
			}
		}
		return null;
	}

	var handle_mouse_hitobject = null;

	function handle_mouse_touch_events(event) {
		var model_label = document.getElementById('model_label');
		var type = "";

		if (event.type == "mousedown") {
			type = "ondown";
			krpano.control.layer.addEventListener("mouseup", handle_mouse_touch_events, true);
		}
		else if (event.type == "mousemove") {
			type = "onmove";
		}
		else if (event.type == "mouseup") {
			type = "onup";
			krpano.control.layer.removeEventListener("mouseup", handle_mouse_touch_events, true);
		}
		else if (event.type == device.browser.events.touchstart) {
			type = "ondown";
			krpano.control.layer.addEventListener(device.browser.events.touchend, handle_mouse_touch_events, true);
		}
		else if (event.type == device.browser.events.touchend) {
			type = "onup";
			krpano.control.layer.removeEventListener(device.browser.events.touchend, handle_mouse_touch_events, true);
		}

		// get mouse / touch pos
		var ms = krpano.control.getMousePos(event.changedTouches ? event.changedTouches[0] : event);
		ms.x /= krpano.stagescale;
		ms.y /= krpano.stagescale;

		// is there a object as that pos?
		var hittest = do_object_hittest(ms.x, ms.y);
		var hitobj = hittest?.object ? hittest?.object : null;
		var point = hittest?.point ? hittest?.point : null;

		if (type == "ondown") {
			if (hitobj) {
				hitobj.properties.pressed = true;

				if (hitobj.properties.ondown) {
					hitobj.properties.ondown(hitobj);
				}

				if (hitobj.properties.capture) {
					krpano.mouse.down = true;
					event.stopPropagation();
				}

				event.preventDefault();
				// planeMesh.position.y = point.y;
				selectedObj = hitobj;
				isDown = true;
			}
			else {
				label.innerHTML = "";
				if (model_label !== null) model_label.style.display = 'none';
			}
		}
		else if (type == "onmove") {
			if (hitobj && isDown) {
				var plane_point = do_object_point(ms.x, ms.y);
				update_position(hitobj, plane_point);
				update_position(hitobj.userData.model, plane_point);

				if (model_label !== null) {
					model_label.style.display = 'none';
				}
			}
		}
		else if (type == "onup") {
			if (hitobj && hitobj.properties.enabled) {
				if (hitobj.properties.pressed) {
					hitobj.properties.pressed = false;
					if (hitobj.properties.onup) {
						hitobj.properties.onup(hitobj);
						if (model_label !== null) {
							model_label.style.display = 'block';
						}
					}
				}
			}
			isDown = false;
			krpano.mouse.down = false;
		}
	}

	function handle_mouse_hovering() {
		// check mouse over state
		if (krpano.mouse.down == false)		// currently not dragging?
		{
			var hitobj = do_object_hittest(krpano.mouse.x, krpano.mouse.y)?.object;

			if (hitobj != handle_mouse_hitobject) {
				if (handle_mouse_hitobject) {
					handle_mouse_hitobject.properties.hovering = false;
					if (handle_mouse_hitobject.properties.onout) handle_mouse_hitobject.properties.onout(handle_mouse_hitobject);
				}

				if (hitobj) {
					hitobj.properties.hovering = true;
					if (hitobj.properties.onover) hitobj.properties.onover(hitobj);
				}

				handle_mouse_hitobject = hitobj;
			}

			if (handle_mouse_hitobject)// || (krpano.display.stereo == false && krpano.display.hotspotrenderer != "webgl"))
			{
				krpano.control.layer.style.cursor = krpano.cursors.hit;
			}
			else {
				krpano.cursors.update();
			}
		}
	}

	function update_scene() {
		// animate objects
		if (selectedObj && window.isAnimate) {
			rotate_object(selectedObj.userData.model);
		}
		handle_mouse_hovering();
	}

	function rotate_object(object) {
		object.rotation.y += 0.005;
	}

	function update_position(hitobj, position) {
		if (position)
			hitobj.position.set(position.x, hitobj.position.y, position.z);
	}
}
