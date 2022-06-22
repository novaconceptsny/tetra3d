<div class="row">
    <x-backend::inputs.input col="col" name="view[hlookat]" label="hlookat" :value="$spot->xml->view['hlookat'] ?? 0"/>
    <x-backend::inputs.input col="col" name="view[vlookat]" label="vlookat" :value="$spot->xml->view['vlookat'] ?? 0"/>
    <x-backend::inputs.input col="col" name="view[fov]" label="fov" :value="$spot->xml->view['fov'] ?? 90"/>
    <x-backend::inputs.input col="col" name="view[fovmax]" label="fovmax" :value="$spot->xml->view['fovmax'] ?? 120"/>
</div>
