<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">About Page Configuration</h1>
          <p class="text-gray-600">Manage content for the About page sections</p>
        </div>
      </div>
    </template>

    <div class="py-6 space-y-6">
      <form @submit.prevent="submit" class="space-y-6">
        
        <!-- Our Story Section -->
        <Card>
          <CardHeader>
            <CardTitle>Our Story Section</CardTitle>
            <CardDescription>Configure the Our Story section content</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_our_story_title_id">Title (Indonesian)</Label>
                <Input 
                  id="about_our_story_title_id"
                  v-model="form.about_our_story_title_id" 
                  :error="form.errors.about_our_story_title_id"
                />
                <InputError :message="form.errors.about_our_story_title_id" />
              </div>
              <div>
                <Label for="about_our_story_title_en">Title (English)</Label>
                <Input 
                  id="about_our_story_title_en"
                  v-model="form.about_our_story_title_en" 
                  :error="form.errors.about_our_story_title_en"
                />
                <InputError :message="form.errors.about_our_story_title_en" />
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_our_story_content_id">Content (Indonesian)</Label>
                <Textarea 
                  id="about_our_story_content_id"
                  v-model="form.about_our_story_content_id" 
                  rows="6"
                  :error="form.errors.about_our_story_content_id"
                />
                <InputError :message="form.errors.about_our_story_content_id" />
              </div>
              <div>
                <Label for="about_our_story_content_en">Content (English)</Label>
                <Textarea 
                  id="about_our_story_content_en"
                  v-model="form.about_our_story_content_en" 
                  rows="6"
                  :error="form.errors.about_our_story_content_en"
                />
                <InputError :message="form.errors.about_our_story_content_en" />
              </div>
            </div>

            <div>
              <Label for="about_our_story_image">Image URL</Label>
              <Input 
                id="about_our_story_image"
                v-model="form.about_our_story_image" 
                placeholder="e.g., /img/story.png"
                :error="form.errors.about_our_story_image"
              />
              <InputError :message="form.errors.about_our_story_image" />
            </div>
          </CardContent>
        </Card>

        <!-- Vision Section -->
        <Card>
          <CardHeader>
            <CardTitle>Vision Section</CardTitle>
            <CardDescription>Configure the Vision section content</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_vision_title_id">Title (Indonesian)</Label>
                <Input 
                  id="about_vision_title_id"
                  v-model="form.about_vision_title_id" 
                  :error="form.errors.about_vision_title_id"
                />
                <InputError :message="form.errors.about_vision_title_id" />
              </div>
              <div>
                <Label for="about_vision_title_en">Title (English)</Label>
                <Input 
                  id="about_vision_title_en"
                  v-model="form.about_vision_title_en" 
                  :error="form.errors.about_vision_title_en"
                />
                <InputError :message="form.errors.about_vision_title_en" />
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_vision_content_id">Content (Indonesian)</Label>
                <Textarea 
                  id="about_vision_content_id"
                  v-model="form.about_vision_content_id" 
                  rows="5"
                  :error="form.errors.about_vision_content_id"
                />
                <InputError :message="form.errors.about_vision_content_id" />
              </div>
              <div>
                <Label for="about_vision_content_en">Content (English)</Label>
                <Textarea 
                  id="about_vision_content_en"
                  v-model="form.about_vision_content_en" 
                  rows="5"
                  :error="form.errors.about_vision_content_en"
                />
                <InputError :message="form.errors.about_vision_content_en" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Mission Section -->
        <Card>
          <CardHeader>
            <CardTitle>Mission Section</CardTitle>
            <CardDescription>Configure the Mission section content</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_mission_title_id">Title (Indonesian)</Label>
                <Input 
                  id="about_mission_title_id"
                  v-model="form.about_mission_title_id" 
                  :error="form.errors.about_mission_title_id"
                />
                <InputError :message="form.errors.about_mission_title_id" />
              </div>
              <div>
                <Label for="about_mission_title_en">Title (English)</Label>
                <Input 
                  id="about_mission_title_en"
                  v-model="form.about_mission_title_en" 
                  :error="form.errors.about_mission_title_en"
                />
                <InputError :message="form.errors.about_mission_title_en" />
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between">
                <Label>Mission Items</Label>
                <Button @click="addMissionItem" type="button" size="sm">
                  <Icon name="mdi:plus" class="mr-2 h-4 w-4" />
                  Add Item
                </Button>
              </div>
              <div class="space-y-4">
                <div 
                  v-for="(item, index) in form.about_mission_items" 
                  :key="item.id" 
                  class="p-4 border rounded-lg"
                >
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Mission Item {{ index + 1 }}</span>
                    <Button 
                      @click="removeMissionItem(index)" 
                      type="button" 
                      variant="destructive" 
                      size="sm"
                      v-if="form.about_mission_items.length > 1"
                    >
                      <Icon name="mdi:trash-can" class="h-4 w-4" />
                    </Button>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Text (Indonesian)</Label>
                      <Textarea v-model="item.text_id" rows="3" />
                    </div>
                    <div>
                      <Label>Text (English)</Label>
                      <Textarea v-model="item.text_en" rows="3" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Logo Philosophy Section -->
        <Card>
          <CardHeader>
            <CardTitle>Logo Philosophy Section</CardTitle>
            <CardDescription>Configure the Logo Philosophy section content</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_logo_philosophy_title_id">Title (Indonesian)</Label>
                <Input 
                  id="about_logo_philosophy_title_id"
                  v-model="form.about_logo_philosophy_title_id" 
                  :error="form.errors.about_logo_philosophy_title_id"
                />
                <InputError :message="form.errors.about_logo_philosophy_title_id" />
              </div>
              <div>
                <Label for="about_logo_philosophy_title_en">Title (English)</Label>
                <Input 
                  id="about_logo_philosophy_title_en"
                  v-model="form.about_logo_philosophy_title_en" 
                  :error="form.errors.about_logo_philosophy_title_en"
                />
                <InputError :message="form.errors.about_logo_philosophy_title_en" />
              </div>
            </div>

            <div>
              <Label for="about_logo_philosophy_image">Logo Image URL</Label>
              <Input 
                id="about_logo_philosophy_image"
                v-model="form.about_logo_philosophy_image" 
                placeholder="e.g., /img/logo.png"
                :error="form.errors.about_logo_philosophy_image"
              />
              <InputError :message="form.errors.about_logo_philosophy_image" />
            </div>

            <div>
              <div class="flex items-center justify-between">
                <Label>Philosophy Points</Label>
                <Button @click="addPhilosophyPoint" type="button" size="sm">
                  <Icon name="mdi:plus" class="mr-2 h-4 w-4" />
                  Add Point
                </Button>
              </div>
              <div class="space-y-4">
                <div 
                  v-for="(point, index) in form.about_logo_philosophy_points" 
                  :key="point.id" 
                  class="p-4 border rounded-lg"
                >
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Philosophy Point {{ index + 1 }}</span>
                    <Button 
                      @click="removePhilosophyPoint(index)" 
                      type="button" 
                      variant="destructive" 
                      size="sm"
                      v-if="form.about_logo_philosophy_points.length > 1"
                    >
                      <Icon name="mdi:trash-can" class="h-4 w-4" />
                    </Button>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Text (Indonesian)</Label>
                      <Textarea v-model="point.text_id" rows="2" />
                    </div>
                    <div>
                      <Label>Text (English)</Label>
                      <Textarea v-model="point.text_en" rows="2" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- F.A.S.T Values Section -->
        <Card>
          <CardHeader>
            <CardTitle>F.A.S.T Values Section</CardTitle>
            <CardDescription>Configure the F.A.S.T Values (Flexible, Agile, Solution-oriented, Trustworthy)</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_fast_values_title_id">Title (Indonesian)</Label>
                <Input 
                  id="about_fast_values_title_id"
                  v-model="form.about_fast_values_title_id" 
                  :error="form.errors.about_fast_values_title_id"
                />
                <InputError :message="form.errors.about_fast_values_title_id" />
              </div>
              <div>
                <Label for="about_fast_values_title_en">Title (English)</Label>
                <Input 
                  id="about_fast_values_title_en"
                  v-model="form.about_fast_values_title_en" 
                  :error="form.errors.about_fast_values_title_en"
                />
                <InputError :message="form.errors.about_fast_values_title_en" />
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_fast_values_subtitle_id">Subtitle (Indonesian)</Label>
                <Textarea 
                  id="about_fast_values_subtitle_id"
                  v-model="form.about_fast_values_subtitle_id" 
                  rows="3"
                  :error="form.errors.about_fast_values_subtitle_id"
                />
                <InputError :message="form.errors.about_fast_values_subtitle_id" />
              </div>
              <div>
                <Label for="about_fast_values_subtitle_en">Subtitle (English)</Label>
                <Textarea 
                  id="about_fast_values_subtitle_en"
                  v-model="form.about_fast_values_subtitle_en" 
                  rows="3"
                  :error="form.errors.about_fast_values_subtitle_en"
                />
                <InputError :message="form.errors.about_fast_values_subtitle_en" />
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between">
                <Label>F.A.S.T Values Items</Label>
                <Button @click="addFastValue" type="button" size="sm">
                  <Icon name="mdi:plus" class="mr-2 h-4 w-4" />
                  Add Value
                </Button>
              </div>
              <div class="space-y-4">
                <div 
                  v-for="(item, index) in form.about_fast_values_items" 
                  :key="item.id"
                  class="border rounded-lg p-4"
                >
                  <div class="flex justify-between items-start mb-4">
                    <h4 class="font-medium">Value {{ index + 1 }}</h4>
                    <Button 
                      @click="removeFastValue(index)" 
                      type="button" 
                      variant="outline" 
                      size="sm"
                      class="text-red-600 hover:text-red-700"
                    >
                      <Icon name="mdi:trash-can" class="h-4 w-4" />
                    </Button>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                      <Label>Title (Indonesian)</Label>
                      <Input v-model="item.title_id" />
                    </div>
                    <div>
                      <Label>Title (English)</Label>
                      <Input v-model="item.title_en" />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                      <Label>Description (Indonesian)</Label>
                      <Textarea v-model="item.description_id" rows="3" />
                    </div>
                    <div>
                      <Label>Description (English)</Label>
                      <Textarea v-model="item.description_en" rows="3" />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Icon URL/Path</Label>
                      <Input v-model="item.icon" placeholder="e.g., /img/icons/flexible.svg" />
                    </div>
                    <div>
                      <Label>Order</Label>
                      <Input v-model.number="item.order" type="number" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- I.D.C Values Section -->
        <Card>
          <CardHeader>
            <CardTitle>I.D.C Values Section</CardTitle>
            <CardDescription>Configure the I.D.C Values (Integrity, Dependable, Competent)</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_idc_values_title_id">Title (Indonesian)</Label>
                <Input 
                  id="about_idc_values_title_id"
                  v-model="form.about_idc_values_title_id" 
                  :error="form.errors.about_idc_values_title_id"
                />
                <InputError :message="form.errors.about_idc_values_title_id" />
              </div>
              <div>
                <Label for="about_idc_values_title_en">Title (English)</Label>
                <Input 
                  id="about_idc_values_title_en"
                  v-model="form.about_idc_values_title_en" 
                  :error="form.errors.about_idc_values_title_en"
                />
                <InputError :message="form.errors.about_idc_values_title_en" />
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_idc_values_subtitle_id">Subtitle (Indonesian)</Label>
                <Textarea 
                  id="about_idc_values_subtitle_id"
                  v-model="form.about_idc_values_subtitle_id" 
                  rows="3"
                  :error="form.errors.about_idc_values_subtitle_id"
                />
                <InputError :message="form.errors.about_idc_values_subtitle_id" />
              </div>
              <div>
                <Label for="about_idc_values_subtitle_en">Subtitle (English)</Label>
                <Textarea 
                  id="about_idc_values_subtitle_en"
                  v-model="form.about_idc_values_subtitle_en" 
                  rows="3"
                  :error="form.errors.about_idc_values_subtitle_en"
                />
                <InputError :message="form.errors.about_idc_values_subtitle_en" />
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between">
                <Label>I.D.C Values Items</Label>
                <Button @click="addIdcValue" type="button" size="sm">
                  <Icon name="mdi:plus" class="mr-2 h-4 w-4" />
                  Add Value
                </Button>
              </div>
              <div class="space-y-4">
                <div 
                  v-for="(item, index) in form.about_idc_values_items" 
                  :key="item.id"
                  class="border rounded-lg p-4"
                >
                  <div class="flex justify-between items-start mb-4">
                    <h4 class="font-medium">Value {{ index + 1 }}</h4>
                    <Button 
                      @click="removeIdcValue(index)" 
                      type="button" 
                      variant="outline" 
                      size="sm"
                      class="text-red-600 hover:text-red-700"
                    >
                      <Icon name="mdi:trash-can" class="h-4 w-4" />
                    </Button>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                      <Label>Title (Indonesian)</Label>
                      <Input v-model="item.title_id" />
                    </div>
                    <div>
                      <Label>Title (English)</Label>
                      <Input v-model="item.title_en" />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                      <Label>Description (Indonesian)</Label>
                      <Textarea v-model="item.description_id" rows="3" />
                    </div>
                    <div>
                      <Label>Description (English)</Label>
                      <Textarea v-model="item.description_en" rows="3" />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Icon URL/Path</Label>
                      <Input v-model="item.icon" placeholder="e.g., /img/icons/integrity.svg" />
                    </div>
                    <div>
                      <Label>Order</Label>
                      <Input v-model.number="item.order" type="number" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Closing Statement -->
        <Card>
          <CardHeader>
            <CardTitle>Closing Statement</CardTitle>
            <CardDescription>Configure the closing statement for the About page</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="about_closing_statement_id">Statement (Indonesian)</Label>
                <Textarea 
                  id="about_closing_statement_id"
                  v-model="form.about_closing_statement_id" 
                  rows="4"
                  :error="form.errors.about_closing_statement_id"
                />
                <InputError :message="form.errors.about_closing_statement_id" />
              </div>
              <div>
                <Label for="about_closing_statement_en">Statement (English)</Label>
                <Textarea 
                  id="about_closing_statement_en"
                  v-model="form.about_closing_statement_en" 
                  rows="4"
                  :error="form.errors.about_closing_statement_en"
                />
                <InputError :message="form.errors.about_closing_statement_en" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <Button type="submit" :disabled="form.processing">
            <Icon 
              name="mdi:loading" 
              class="mr-2 h-4 w-4 animate-spin" 
              v-if="form.processing" 
            />
            <Icon name="mdi:content-save" class="mr-2 h-4 w-4" v-else />
            {{ form.processing ? 'Saving...' : 'Save Configuration' }}
          </Button>
        </div>

      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  configurations: Object
})

const form = useForm({
  // Our Story
  about_our_story_title_id: props.configurations.about_our_story_title_id || '',
  about_our_story_title_en: props.configurations.about_our_story_title_en || '',
  about_our_story_content_id: props.configurations.about_our_story_content_id || '',
  about_our_story_content_en: props.configurations.about_our_story_content_en || '',
  about_our_story_image: props.configurations.about_our_story_image || '',
  
  // Vision
  about_vision_title_id: props.configurations.about_vision_title_id || '',
  about_vision_title_en: props.configurations.about_vision_title_en || '',
  about_vision_content_id: props.configurations.about_vision_content_id || '',
  about_vision_content_en: props.configurations.about_vision_content_en || '',
  
  // Mission
  about_mission_title_id: props.configurations.about_mission_title_id || '',
  about_mission_title_en: props.configurations.about_mission_title_en || '',
  about_mission_items: props.configurations.about_mission_items || [],
  
  // Logo Philosophy
  about_logo_philosophy_title_id: props.configurations.about_logo_philosophy_title_id || '',
  about_logo_philosophy_title_en: props.configurations.about_logo_philosophy_title_en || '',
  about_logo_philosophy_image: props.configurations.about_logo_philosophy_image || '',
  about_logo_philosophy_points: props.configurations.about_logo_philosophy_points || [],
  
  // F.A.S.T Values
  about_fast_values_title_id: props.configurations.about_fast_values_title_id || '',
  about_fast_values_title_en: props.configurations.about_fast_values_title_en || '',
  about_fast_values_subtitle_id: props.configurations.about_fast_values_subtitle_id || '',
  about_fast_values_subtitle_en: props.configurations.about_fast_values_subtitle_en || '',
  about_fast_values_items: props.configurations.about_fast_values_items || [],
  
  // I.D.C Values
  about_idc_values_title_id: props.configurations.about_idc_values_title_id || '',
  about_idc_values_title_en: props.configurations.about_idc_values_title_en || '',
  about_idc_values_subtitle_id: props.configurations.about_idc_values_subtitle_id || '',
  about_idc_values_subtitle_en: props.configurations.about_idc_values_subtitle_en || '',
  about_idc_values_items: props.configurations.about_idc_values_items || [],
  
  // Closing Statement
  about_closing_statement_id: props.configurations.about_closing_statement_id || '',
  about_closing_statement_en: props.configurations.about_closing_statement_en || '',
})

const submit = () => {
  // Ensure proper order for array items
  form.about_mission_items.forEach((item, index) => {
    item.order = index + 1
  })
  
  form.about_logo_philosophy_points.forEach((point, index) => {
    point.order = index + 1
  })
  
  form.about_fast_values_items.forEach((item, index) => {
    item.order = index + 1
  })
  
  form.about_idc_values_items.forEach((item, index) => {
    item.order = index + 1
  })
  
  form.put(route('system.configurations.about.update'), {
    onSuccess: () => {
      // Show success message or handle success
    },
  })
}

const addMissionItem = () => {
  const newId = Math.max(...form.about_mission_items.map(item => item.id), 0) + 1
  form.about_mission_items.push({
    id: newId,
    text_id: '',
    text_en: '',
    order: form.about_mission_items.length + 1
  })
}

const removeMissionItem = (index) => {
  form.about_mission_items.splice(index, 1)
}

const addPhilosophyPoint = () => {
  const newId = Math.max(...form.about_logo_philosophy_points.map(point => point.id), 0) + 1
  form.about_logo_philosophy_points.push({
    id: newId,
    text_id: '',
    text_en: '',
    order: form.about_logo_philosophy_points.length + 1
  })
}

const removePhilosophyPoint = (index) => {
  form.about_logo_philosophy_points.splice(index, 1)
}

// F.A.S.T Values methods
const addFastValue = () => {
  const newId = Math.max(...form.about_fast_values_items.map(item => item.id), 0) + 1
  form.about_fast_values_items.push({
    id: newId,
    title_id: '',
    title_en: '',
    description_id: '',
    description_en: '',
    icon: '',
    order: form.about_fast_values_items.length + 1
  })
}

const removeFastValue = (index) => {
  form.about_fast_values_items.splice(index, 1)
}

// I.D.C Values methods
const addIdcValue = () => {
  const newId = Math.max(...form.about_idc_values_items.map(item => item.id), 0) + 1
  form.about_idc_values_items.push({
    id: newId,
    title_id: '',
    title_en: '',
    description_id: '',
    description_en: '',
    icon: '',
    order: form.about_idc_values_items.length + 1
  })
}

const removeIdcValue = (index) => {
  form.about_idc_values_items.splice(index, 1)
}
</script>