@props([
    'model' => null,
    'collection' => null,
    'maxItems' => null,
    'name' => null,
    'rules' => '',
    'fieldsView' => 'backend.includes.media_fields.empty',
    'propertiesView' => 'backend.includes.media_fields.empty',
    'multiple' => false
])

@if($model)
    <x-media-library-collection
        :name="$name" :rules="$rules"
        :fields-view="$fieldsView"
        :multiple="$multiple"
        :model="$model" :collection="$collection"
        :max-items="$maxItems"
    />
@else
    <x-media-library-attachment
        :name="$name" :rules="$rules"
        :max-items="$maxItems"
        :multiple="$multiple"
        :properties-view="$propertiesView"
    />
@endif

