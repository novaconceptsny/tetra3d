<div class="row">
    <x-backend::inputs.input col="col" name="view[hlookat]" label="hlookat" :value="$spot->xml->view['hlookat'] ?? ''"/>
    <x-backend::inputs.input col="col" name="view[vlookat]" label="vlookat" :value="$spot->xml->view['vlookat'] ?? ''"/>
    <x-backend::inputs.input col="col" name="view[fov]" label="fov" :value="$spot->xml->view['fov'] ?? ''"/>
    <x-backend::inputs.input col="col" name="view[fovmax]" label="fovmax" :value="$spot->xml->view['fovmax'] ?? ''"/>
</div>
