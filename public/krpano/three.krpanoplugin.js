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
			krpano.control.layer.addEventListener(device.browser.events.touchmove, handle_mouse_touch_events, true);
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

		// renderer.resetGLState();
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

		// renderer.resetGLState();
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

		// renderer.resetState();
		// renderer.resetGLState();
		// renderer.state.reset();
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

		// renderer.resetState();
		// renderer.resetGLState();
		// renderer.state.reset();
		// important - restore the krpano WebGL state for correct krpano rendering
		restore_krpano_WebGL_state();
	}

	// -----------------------------------------------------------------------
	// ThreeJS User Content - START HERE

	window.isAnimate = false;
	var selectedObj = null;
	var gizmoObj = null;
	var isDown = false;
	var selected_surface_id = null;
	var canMove = false;
	var plane_point_temp = null;
	var direction = '';

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

		camera_hittest_raycaster.ray.direction.set(pointer.x, pointer.y, 1.0).unproject(camera).normalize();
		var intersects = camera_hittest_raycaster.intersectObjects(scene.children, true);
		var i;
		var object = null;
		var gizmo = null;
		var surface = null;
		var point = null;

		for (i = 0; i < intersects.length; i++) {
			var obj = intersects[i].object;
			if (obj.name == 'interaction-model') {
				object = obj.userData.model;
				point = intersects[i].point;
			}

			if (obj.name == 'gizmoPlane' || obj.name == 'arrow_x' || obj.name == 'arrow_z' || obj.name == 'direct_x' || obj.name == 'direct_z') {
				gizmo = obj;
				point = intersects[i].point;
			}

			if (obj.userData.type === "surface") {
				obj = intersects[0].object;
				surface = obj;
				point = intersects[i].point;
				object = obj;
			}
		}

		if (intersects.length > 0) {
			var obj = intersects[0].object;
		}
		if (point)
			return { object: object, gizmo: gizmo, point: point };
		else return null;
	}

	function do_object_point(mx, my) {
		const pointer = new THREE.Vector2();
		pointer.x = (mx / krpano.area.pixelwidth) * 2.0 - 1.0;
		pointer.y = -(my / krpano.area.pixelheight) * 2.0 + 1.0;

		if (krpano.display.stereo) {
			mouse_x += (mouse_x < 0.0 ? +1 : -1) * (1.0 - Number(krpano.display.stereooverlap)) * 0.5;
		}

		camera_hittest_raycaster.ray.direction.set(pointer.x, pointer.y, 1.0).unproject(camera).normalize();
		var intersects = camera_hittest_raycaster.intersectObjects(scene.children, true);
		for (var i = 0; i < intersects.length; i++) {
			obj = intersects[i].object;
			if (obj && obj.name == 'surface-model') {
				return intersects[i].point;
			}
		}
		return null;
	}

	function handle_mouse_touch_events(event) {
		var model_label = document.getElementById('model_label');
		var type = "";
		var hitobj = null;
		var gizmo = null;
		var point = null;

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
		else if (event.type == device.browser.events.touchmove) {
			type = "onmove";
		}

		// get mouse / touch pos
		var ms = krpano.control.getMousePos(event.changedTouches ? event.changedTouches[0] : event);
		ms.x /= krpano.stagescale;
		ms.y /= krpano.stagescale;

		// is there a object as that pos?
		var hittest = do_object_hittest(ms.x, ms.y);
		if (hittest !== null) {
			hitobj = hittest.object;
			gizmo = hittest.gizmo;
			point = hittest.point;
		}

		if (type == "ondown") {
			if (selectedObj) {
				scene.remove(selectedObj.userData.gizmo);
				selectedObj.userData.model.traverse((obj) => {
					if (obj.name == 'sculpture-model') {
						obj.material.emissive.setHex(0x000000);
						obj.material.transparent = false;
						obj.material.opacity = 1;
						obj.material.needsUpdate = true;
					}
				});
			}
			if (hitobj || gizmo) {
				isDown = true;
				krpano.mouse.down = true;

				if (gizmo) {
					event.preventDefault();
					event.stopPropagation();
					selectedObj = gizmo.parent.userData.temp;
					gizmoObj = gizmo.parent;
					plane_point_temp = point;
					canMove = true;
					if (gizmo.name == 'arrow_x' || gizmo.name == 'direct_x') direction = 'x';
					if (gizmo.name == 'arrow_z' || gizmo.name == 'direct_z') direction = 'z';
					if (gizmo.name == 'gizmoPlane') direction = 'xz';
				} else {
					if (hitobj.userData.type === "surface") {
						selected_surface_id = hitobj.userData.surface_id;
					} else {
						event.preventDefault();
						event.stopPropagation();
						selectedObj = hitobj.userData.temp;
					}
				}
			} else {
				label.innerHTML = "";

				if (model_label !== null)
					model_label.style.display = 'none';

				if (gizmoObj)
					scene.remove(gizmoObj);
			}
		}
		else if (type == "onmove") {
			event.preventDefault();
			event.stopPropagation();
			if (canMove && isDown) {

				var plane_point = do_object_point(ms.x, ms.y);

				update_position(selectedObj, plane_point, plane_point_temp, direction);
				update_position(selectedObj.userData.model, plane_point, plane_point_temp, direction);
				update_position(gizmoObj, plane_point, plane_point_temp, direction);

				if (plane_point !== null)
					plane_point_temp = plane_point;

				if (model_label !== null) {
					model_label.style.display = 'none';
				}

				selectedObj.userData.changed = true;
			}
		}
		else if (type == "onup") {
			if (selectedObj && isDown) {
				selectedObj.properties.onup(selectedObj);

				if (model_label !== null) {
					model_label.style.display = 'block';
				}

				selectedObj.userData.model.traverse((obj) => {
					if (obj instanceof THREE.Mesh) {
						if (obj.name == 'sculpture-model') {
							obj.material.emissive.setHex(0x001f1f);
							obj.material.transparent = true;
							obj.material.opacity = 0.8;
							obj.material.needsUpdate = true;
						}
					}
				});

				// if (!canMove) {
				make_gizmo(selectedObj);
				// }
			}
			if (hitobj && isDown && hitobj.userData.type == "surface" && selected_surface_id === hitobj.userData.surface_id) {
				var hlookat = krpano.view.hlookat;
				var vlookat = krpano.view.vlookat;
				var urlStr = "/surfaces/" + hitobj.userData.surface_id + "?spot_id=" + hitobj.userData.spot_id + "&layout_id=" + hitobj.userData.layout_id + "&hlookat=" + hlookat + "&vlookat=" + vlookat;
				console.log(hitobj.userData.layout_id, "layout_id", urlStr)
				window.location.href = urlStr;
			}
			isDown = false;
			krpano.mouse.down = false;
			canMove = false;
		}
	}

	function make_gizmo(object) {
		var gizmo = new THREE.Group();

		var arrowGeometry = new THREE.ConeGeometry(4, 8, 32);
		var directGeometry = new THREE.CylinderGeometry(2, 2, 30, 32);
		var gizmoPlaneGeometry = new THREE.PlaneGeometry(15, 15);

		var arrow_x = new THREE.Mesh(arrowGeometry, new THREE.MeshBasicMaterial({ color: 0xff0000, transparent: false, opacity: 0.8 }));
		var arrow_z = new THREE.Mesh(arrowGeometry, new THREE.MeshBasicMaterial({ color: 0x00ff00, transparent: false, opacity: 0.8 }));
		var direct_x = new THREE.Mesh(directGeometry, new THREE.MeshBasicMaterial({ color: 0xff0000, transparent: false, opacity: 0.8 }));
		var direct_z = new THREE.Mesh(directGeometry, new THREE.MeshBasicMaterial({ color: 0x00ff00, transparent: false, opacity: 0.8 }));
		var gizmoPlane = new THREE.Mesh(gizmoPlaneGeometry, new THREE.MeshBasicMaterial({ color: 0x0000ff, side: THREE.DoubleSide, transparent: false, opacity: 0.8 }));

		arrow_x.position.set(30, 0, 0);
		arrow_z.position.set(0, 0, 30);
		direct_x.position.set(15, 0, 0);
		direct_z.position.set(0, 0, 15);
		gizmoPlane.position.set(7.5, 0, 7.5);

		arrow_x.name = 'arrow_x';
		arrow_z.name = 'arrow_z';
		direct_x.name = 'direct_x';
		direct_z.name = 'direct_z';
		gizmoPlane.name = 'gizmoPlane';

		arrow_x.rotation.z = - Math.PI / 2;
		arrow_z.rotation.x = Math.PI / 2;
		direct_x.rotation.z = Math.PI / 2;
		direct_z.rotation.x = Math.PI / 2;
		gizmoPlane.rotation.x = Math.PI / 2;

		gizmo.add(arrow_x);
		gizmo.add(arrow_z);
		gizmo.add(direct_x);
		gizmo.add(direct_z);
		gizmo.add(gizmoPlane);

		gizmo.userData.temp = object;
		object.userData.gizmo = gizmo;
		scene.add(gizmo);

		gizmo.position.set(object.position.x, object.position.y, object.position.z);
	}

	function handle_mouse_hovering() {
		// check mouse over state
		if (krpano.mouse.down == false)		// currently not dragging?
		{
			var hittest = do_object_hittest(krpano.mouse.x, krpano.mouse.y);

			if (hittest) {
				if (hittest.object || hittest.gizmo) {
					krpano.control.layer.style.cursor = krpano.cursors.hit;
				} else {
					krpano.cursors.update();
				}
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
		object.userData.temp.rotation.y += 0.005;
	}

	function update_position(hitobj, position, temp, direction) {
		if (position) {
			if (direction == 'x') hitobj.position.set(hitobj.position.x + position.x - temp.x, hitobj.position.y, hitobj.position.z);
			if (direction == 'z') hitobj.position.set(hitobj.position.x, hitobj.position.y, hitobj.position.z + position.z - temp.z);
			if (direction == 'xz') hitobj.position.set(hitobj.position.x + position.x - temp.x, hitobj.position.y, hitobj.position.z + position.z - temp.z);
		}
	}
}
