<template>
  <div class="space-y-6">
    
    <!-- Our Story Section -->
    <div v-if="section === 'our-story'">
      <Card>
        <CardHeader>
          <CardTitle>Our Story Section</CardTitle>
          <CardDescription>
            Configure the Our Story section content
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="about_our_story_title_id">Title (Indonesian)</Label>
              <Input 
                id="about_our_story_title_id"
                v-model="form.about_our_story_title_id" 
                placeholder="Enter title in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label for="about_our_story_title_en">Title (English)</Label>
              <Input 
                id="about_our_story_title_en"
                v-model="form.about_our_story_title_en" 
                placeholder="Enter title in English"
                :disabled="isLoading"
              />
            </div>
          </div>
          
          <div class="space-y-4">
            <div class="space-y-2">
              <Label>Content (Indonesian)</Label>
              <RichTextEditor
                v-model="form.about_our_story_content_id"
                placeholder="Write our story content in Indonesian..."
                :height="250"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label>Content (English)</Label>
              <RichTextEditor
                v-model="form.about_our_story_content_en"
                placeholder="Write our story content in English..."
                :height="250"
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="space-y-2">
            <Label>Story Image</Label>
            <div class="border-2 border-dashed border-input rounded-lg p-4 dark:border-input">
              <div v-if="ourStoryImagePreview || form.about_our_story_image" class="space-y-2">
                <img :src="ourStoryImagePreview || getImageUrl(form.about_our_story_image)" alt="Story Image Preview" class="w-full h-32 object-cover rounded" />
                <Button size="sm" variant="outline" @click="$refs.ourStoryImageInput.click()">
                  <Upload class="w-4 h-4 mr-2" />
                  Change Image
                </Button>
              </div>
              <div v-else class="text-center">
                <Image class="mx-auto h-8 w-8 text-gray-400" />
                <Button size="sm" variant="outline" @click="$refs.ourStoryImageInput.click()">
                  <Upload class="w-4 h-4 mr-2" />
                  Upload Image
                </Button>
              </div>
              <input
                ref="ourStoryImageInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleOurStoryImageUpload"
              />
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-4">
            <Button 
              @click="saveSettings" 
              :disabled="isSaving || isLoading"
              class="min-w-[120px]"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
              <Save v-else class="w-4 h-4 mr-2" />
              {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Vision Section -->
    <div v-if="section === 'vision'">
      <Card>
        <CardHeader>
          <CardTitle>Vision Section</CardTitle>
          <CardDescription>
            Configure the Vision section content
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="about_vision_title_id">Title (Indonesian)</Label>
              <Input 
                id="about_vision_title_id"
                v-model="form.about_vision_title_id" 
                placeholder="Enter title in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label for="about_vision_title_en">Title (English)</Label>
              <Input 
                id="about_vision_title_en"
                v-model="form.about_vision_title_en" 
                placeholder="Enter title in English"
                :disabled="isLoading"
              />
            </div>
          </div>
          
          <div class="space-y-4">
            <div class="space-y-2">
              <Label>Content (Indonesian)</Label>
              <RichTextEditor
                v-model="form.about_vision_content_id"
                placeholder="Write vision content in Indonesian..."
                :height="200"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label>Content (English)</Label>
              <RichTextEditor
                v-model="form.about_vision_content_en"
                placeholder="Write vision content in English..."
                :height="200"
                :disabled="isLoading"
              />
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-4">
            <Button 
              @click="saveSettings" 
              :disabled="isSaving || isLoading"
              class="min-w-[120px]"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
              <Save v-else class="w-4 h-4 mr-2" />
              {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Mission Section -->
    <div v-if="section === 'mission'">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center justify-between">
            <span>Mission Section</span>
            <Button 
              @click="addMissionItem" 
              variant="outline" 
              size="sm"
            >
              <Plus class="w-4 h-4 mr-2" />
              Add Item
            </Button>
          </CardTitle>
          <CardDescription>
            Configure the Mission section content
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="about_mission_title_id">Title (Indonesian)</Label>
              <Input 
                id="about_mission_title_id"
                v-model="form.about_mission_title_id" 
                placeholder="Enter title in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label for="about_mission_title_en">Title (English)</Label>
              <Input 
                id="about_mission_title_en"
                v-model="form.about_mission_title_en" 
                placeholder="Enter title in English"
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="space-y-4">
            <div v-if="!form.about_mission_items || form.about_mission_items.length === 0" 
                 class="text-center py-8 text-muted-foreground">
              No mission items configured. Click "Add Item" to create one.
            </div>

            <div v-for="(item, index) in form.about_mission_items" 
                 :key="item.id" 
                 class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
              <div class="flex items-center justify-between">
                <Badge variant="outline">Mission Item {{ index + 1 }}</Badge>
                <Button 
                  @click="removeMissionItem(index)" 
                  variant="destructive" 
                  size="sm"
                  v-if="form.about_mission_items.length > 1"
                >
                  <Trash2 class="w-4 h-4" />
                </Button>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label>Text (Indonesian)</Label>
                  <Textarea 
                    v-model="item.text_id" 
                    rows="3" 
                    placeholder="Enter mission item in Indonesian"
                    :disabled="isLoading"
                  />
                </div>
                <div class="space-y-2">
                  <Label>Text (English)</Label>
                  <Textarea 
                    v-model="item.text_en" 
                    rows="3" 
                    placeholder="Enter mission item in English"
                    :disabled="isLoading"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-4">
            <Button 
              @click="saveSettings" 
              :disabled="isSaving || isLoading"
              class="min-w-[120px]"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
              <Save v-else class="w-4 h-4 mr-2" />
              {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Logo Philosophy Section -->
    <div v-if="section === 'logo-philosophy'">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center justify-between">
            <span>Logo Philosophy Section</span>
            <Button 
              @click="addPhilosophyPoint" 
              variant="outline" 
              size="sm"
            >
              <Plus class="w-4 h-4 mr-2" />
              Add Point
            </Button>
          </CardTitle>
          <CardDescription>
            Configure the Logo Philosophy section content
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="about_logo_philosophy_title_id">Title (Indonesian)</Label>
              <Input 
                id="about_logo_philosophy_title_id"
                v-model="form.about_logo_philosophy_title_id" 
                placeholder="Enter title in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label for="about_logo_philosophy_title_en">Title (English)</Label>
              <Input 
                id="about_logo_philosophy_title_en"
                v-model="form.about_logo_philosophy_title_en" 
                placeholder="Enter title in English"
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="space-y-2">
            <Label>Logo Image</Label>
            <div class="border-2 border-dashed border-input rounded-lg p-4 dark:border-input">
              <div v-if="logoPhilosophyImagePreview || form.about_logo_philosophy_image" class="space-y-2">
                <img :src="logoPhilosophyImagePreview || getImageUrl(form.about_logo_philosophy_image)" alt="Logo Preview" class="w-32 h-32 object-contain rounded mx-auto" />
                <Button size="sm" variant="outline" @click="$refs.logoPhilosophyImageInput.click()">
                  <Upload class="w-4 h-4 mr-2" />
                  Change Image
                </Button>
              </div>
              <div v-else class="text-center">
                <Image class="mx-auto h-8 w-8 text-gray-400" />
                <Button size="sm" variant="outline" @click="$refs.logoPhilosophyImageInput.click()">
                  <Upload class="w-4 h-4 mr-2" />
                  Upload Image
                </Button>
              </div>
              <input
                ref="logoPhilosophyImageInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleLogoPhilosophyImageUpload"
              />
            </div>
          </div>

          <div class="space-y-4">
            <div v-if="!form.about_logo_philosophy_points || form.about_logo_philosophy_points.length === 0" 
                 class="text-center py-8 text-muted-foreground">
              No philosophy points configured. Click "Add Point" to create one.
            </div>

            <div v-for="(point, index) in form.about_logo_philosophy_points" 
                 :key="point.id" 
                 class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
              <div class="flex items-center justify-between">
                <Badge variant="outline">Philosophy Point {{ index + 1 }}</Badge>
                <Button 
                  @click="removePhilosophyPoint(index)" 
                  variant="destructive" 
                  size="sm"
                  v-if="form.about_logo_philosophy_points.length > 1"
                >
                  <Trash2 class="w-4 h-4" />
                </Button>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label>Text (Indonesian)</Label>
                  <Textarea 
                    v-model="point.text_id" 
                    rows="2" 
                    placeholder="Enter philosophy point in Indonesian"
                    :disabled="isLoading"
                  />
                </div>
                <div class="space-y-2">
                  <Label>Text (English)</Label>
                  <Textarea 
                    v-model="point.text_en" 
                    rows="2" 
                    placeholder="Enter philosophy point in English"
                    :disabled="isLoading"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-4">
            <Button 
              @click="saveSettings" 
              :disabled="isSaving || isLoading"
              class="min-w-[120px]"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
              <Save v-else class="w-4 h-4 mr-2" />
              {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- F.A.S.T Values Section -->
    <div v-if="section === 'fast-values'">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center justify-between">
            <span>F.A.S.T Values Section</span>
            <Button 
              @click="addFastValue" 
              variant="outline" 
              size="sm"
            >
              <Plus class="w-4 h-4 mr-2" />
              Add Value
            </Button>
          </CardTitle>
          <CardDescription>
            Configure F.A.S.T Values (Flexible, Agile, Solution-oriented, Trustworthy)
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="about_fast_values_title_id">Title (Indonesian)</Label>
              <Input 
                id="about_fast_values_title_id"
                v-model="form.about_fast_values_title_id" 
                placeholder="Enter title in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label for="about_fast_values_title_en">Title (English)</Label>
              <Input 
                id="about_fast_values_title_en"
                v-model="form.about_fast_values_title_en" 
                placeholder="Enter title in English"
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label>Subtitle (Indonesian)</Label>
              <Textarea 
                v-model="form.about_fast_values_subtitle_id" 
                rows="3" 
                placeholder="Enter subtitle in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label>Subtitle (English)</Label>
              <Textarea 
                v-model="form.about_fast_values_subtitle_en" 
                rows="3" 
                placeholder="Enter subtitle in English"
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="space-y-4">
            <div v-if="!form.about_fast_values_items || form.about_fast_values_items.length === 0" 
                 class="text-center py-8 text-muted-foreground">
              No F.A.S.T values configured. Click "Add Value" to create one.
            </div>

            <div v-for="(item, index) in form.about_fast_values_items" 
                 :key="item.id" 
                 class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
              <div class="flex items-center justify-between">
                <Badge variant="outline">{{ item.title_id || `Value ${index + 1}` }}</Badge>
                <Button 
                  @click="removeFastValue(index)" 
                  variant="destructive" 
                  size="sm"
                  v-if="form.about_fast_values_items.length > 1"
                >
                  <Trash2 class="w-4 h-4" />
                </Button>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label>Title (Indonesian)</Label>
                  <Input 
                    v-model="item.title_id" 
                    placeholder="e.g., Flexible"
                    :disabled="isLoading"
                  />
                </div>
                <div class="space-y-2">
                  <Label>Title (English)</Label>
                  <Input 
                    v-model="item.title_en" 
                    placeholder="e.g., Flexible"
                    :disabled="isLoading"
                  />
                </div>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label>Description (Indonesian)</Label>
                  <Textarea 
                    v-model="item.description_id" 
                    rows="3" 
                    placeholder="Enter value description in Indonesian"
                    :disabled="isLoading"
                  />
                </div>
                <div class="space-y-2">
                  <Label>Description (English)</Label>
                  <Textarea 
                    v-model="item.description_en" 
                    rows="3" 
                    placeholder="Enter value description in English"
                    :disabled="isLoading"
                  />
                </div>
              </div>
              <div class="space-y-2">
                <Label>Icon Image</Label>
                <div class="border-2 border-dashed border-input rounded-lg p-4 dark:border-input">
                  <div v-if="fastValueIconPreviews[index] || item.icon" class="space-y-2">
                    <img :src="fastValueIconPreviews[index] || getImageUrl(item.icon)" :alt="`${item.title_en || 'Value'} Icon`" class="w-16 h-16 object-contain rounded mx-auto" />
                    <div class="flex justify-center gap-2">
                      <Button size="sm" variant="outline" @click="() => clearFastValueIcon(index)">
                        <X class="w-4 h-4 mr-2" />
                        Remove
                      </Button>
                      <Button size="sm" variant="outline" @click="() => $refs[`fastValueIconInput${index}`][0].click()">
                        <Upload class="w-4 h-4 mr-2" />
                        Change
                      </Button>
                    </div>
                  </div>
                  <div v-else class="text-center">
                    <Image class="mx-auto h-8 w-8 text-gray-400" />
                    <Button size="sm" variant="outline" @click="() => $refs[`fastValueIconInput${index}`][0].click()">
                      <Upload class="w-4 h-4 mr-2" />
                      Upload Icon
                    </Button>
                    <p class="text-sm text-muted-foreground mt-2">PNG, JPG, SVG up to 2MB</p>
                  </div>
                  <input
                    :ref="`fastValueIconInput${index}`"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="(e) => handleFastValueIconUpload(e, index)"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-4">
            <Button 
              @click="saveSettings" 
              :disabled="isSaving || isLoading"
              class="min-w-[120px]"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
              <Save v-else class="w-4 h-4 mr-2" />
              {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- I.D.C Values Section -->
    <div v-if="section === 'idc-values'">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center justify-between">
            <span>I.D.C Values Section</span>
            <Button 
              @click="addIdcValue" 
              variant="outline" 
              size="sm"
            >
              <Plus class="w-4 h-4 mr-2" />
              Add Value
            </Button>
          </CardTitle>
          <CardDescription>
            Configure I.D.C Values (Integrity, Dependable, Competent)
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="about_idc_values_title_id">Title (Indonesian)</Label>
              <Input 
                id="about_idc_values_title_id"
                v-model="form.about_idc_values_title_id" 
                placeholder="Enter title in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label for="about_idc_values_title_en">Title (English)</Label>
              <Input 
                id="about_idc_values_title_en"
                v-model="form.about_idc_values_title_en" 
                placeholder="Enter title in English"
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label>Subtitle (Indonesian)</Label>
              <Textarea 
                v-model="form.about_idc_values_subtitle_id" 
                rows="3" 
                placeholder="Enter subtitle in Indonesian"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label>Subtitle (English)</Label>
              <Textarea 
                v-model="form.about_idc_values_subtitle_en" 
                rows="3" 
                placeholder="Enter subtitle in English"
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="space-y-4">
            <div v-if="!form.about_idc_values_items || form.about_idc_values_items.length === 0" 
                 class="text-center py-8 text-muted-foreground">
              No I.D.C values configured. Click "Add Value" to create one.
            </div>

            <div v-for="(item, index) in form.about_idc_values_items" 
                 :key="item.id" 
                 class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
              <div class="flex items-center justify-between">
                <Badge variant="outline">{{ item.title_id || `Value ${index + 1}` }}</Badge>
                <Button 
                  @click="removeIdcValue(index)" 
                  variant="destructive" 
                  size="sm"
                  v-if="form.about_idc_values_items.length > 1"
                >
                  <Trash2 class="w-4 h-4" />
                </Button>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label>Title (Indonesian)</Label>
                  <Input 
                    v-model="item.title_id" 
                    placeholder="e.g., Integrity"
                    :disabled="isLoading"
                  />
                </div>
                <div class="space-y-2">
                  <Label>Title (English)</Label>
                  <Input 
                    v-model="item.title_en" 
                    placeholder="e.g., Integrity"
                    :disabled="isLoading"
                  />
                </div>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label>Description (Indonesian)</Label>
                  <Textarea 
                    v-model="item.description_id" 
                    rows="3" 
                    placeholder="Enter value description in Indonesian"
                    :disabled="isLoading"
                  />
                </div>
                <div class="space-y-2">
                  <Label>Description (English)</Label>
                  <Textarea 
                    v-model="item.description_en" 
                    rows="3" 
                    placeholder="Enter value description in English"
                    :disabled="isLoading"
                  />
                </div>
              </div>
              <div class="space-y-2">
                <Label>Icon Image</Label>
                <div class="border-2 border-dashed border-input rounded-lg p-4 dark:border-input">
                  <div v-if="idcValueIconPreviews[index] || item.icon" class="space-y-2">
                    <img :src="idcValueIconPreviews[index] || getImageUrl(item.icon)" :alt="`${item.title_en || 'Value'} Icon`" class="w-16 h-16 object-contain rounded mx-auto" />
                    <div class="flex justify-center gap-2">
                      <Button size="sm" variant="outline" @click="() => clearIdcValueIcon(index)">
                        <X class="w-4 h-4 mr-2" />
                        Remove
                      </Button>
                      <Button size="sm" variant="outline" @click="() => $refs[`idcValueIconInput${index}`][0].click()">
                        <Upload class="w-4 h-4 mr-2" />
                        Change
                      </Button>
                    </div>
                  </div>
                  <div v-else class="text-center">
                    <Image class="mx-auto h-8 w-8 text-gray-400" />
                    <Button size="sm" variant="outline" @click="() => $refs[`idcValueIconInput${index}`][0].click()">
                      <Upload class="w-4 h-4 mr-2" />
                      Upload Icon
                    </Button>
                    <p class="text-sm text-muted-foreground mt-2">PNG, JPG, SVG up to 2MB</p>
                  </div>
                  <input
                    :ref="`idcValueIconInput${index}`"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="(e) => handleIdcValueIconUpload(e, index)"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-4">
            <Button 
              @click="saveSettings" 
              :disabled="isSaving || isLoading"
              class="min-w-[120px]"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
              <Save v-else class="w-4 h-4 mr-2" />
              {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Closing Statement -->
    <div v-if="section === 'closing'">
      <Card>
        <CardHeader>
          <CardTitle>Closing Statement</CardTitle>
          <CardDescription>
            Configure the closing statement for the About page
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="space-y-4">
            <div class="space-y-2">
              <Label>Statement (Indonesian)</Label>
              <RichTextEditor
                v-model="form.about_closing_statement_id"
                placeholder="Write closing statement in Indonesian..."
                :height="200"
                :disabled="isLoading"
              />
            </div>
            <div class="space-y-2">
              <Label>Statement (English)</Label>
              <RichTextEditor
                v-model="form.about_closing_statement_en"
                placeholder="Write closing statement in English..."
                :height="200"
                :disabled="isLoading"
              />
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-4">
            <Button 
              @click="saveSettings" 
              :disabled="isSaving || isLoading"
              class="min-w-[120px]"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
              <Save v-else class="w-4 h-4 mr-2" />
              {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

  </div>
</template>

<script setup>
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Badge } from '@/components/ui/badge'
import RichTextEditor from '@/components/ui/RichTextEditor.vue'
import { Save, Plus, Trash2, Loader2, Upload, Image, X } from 'lucide-vue-next'
import { reactive, ref, watch } from 'vue'

const props = defineProps({
  configurations: {
    type: Object,
    default: () => ({})
  },
  isLoading: Boolean,
  isSaving: Boolean,
  section: {
    type: String,
    default: 'our-story'
  }
})

const emit = defineEmits(['save', 'update', 'bulkSave'])

// Initialize form data properly
const initializeFormData = () => {
  const configs = props.configurations
  return {
    // Our Story
    about_our_story_title_id: configs.about_our_story_title_id?.value || '',
    about_our_story_title_en: configs.about_our_story_title_en?.value || '',
    about_our_story_content_id: configs.about_our_story_content_id?.value || '',
    about_our_story_content_en: configs.about_our_story_content_en?.value || '',
    about_our_story_image: configs.about_our_story_image?.value || '',
    
    // Vision
    about_vision_title_id: configs.about_vision_title_id?.value || '',
    about_vision_title_en: configs.about_vision_title_en?.value || '',
    about_vision_content_id: configs.about_vision_content_id?.value || '',
    about_vision_content_en: configs.about_vision_content_en?.value || '',
    
    // Mission
    about_mission_title_id: configs.about_mission_title_id?.value || '',
    about_mission_title_en: configs.about_mission_title_en?.value || '',
    about_mission_items: configs.about_mission_items?.value || [{ id: 1, text_id: '', text_en: '', order: 1 }],
    
    // Logo Philosophy
    about_logo_philosophy_title_id: configs.about_logo_philosophy_title_id?.value || '',
    about_logo_philosophy_title_en: configs.about_logo_philosophy_title_en?.value || '',
    about_logo_philosophy_image: configs.about_logo_philosophy_image?.value || '',
    about_logo_philosophy_points: configs.about_logo_philosophy_points?.value || [{ id: 1, text_id: '', text_en: '', order: 1 }],
    
    // F.A.S.T Values
    about_fast_values_title_id: configs.about_fast_values_title_id?.value || '',
    about_fast_values_title_en: configs.about_fast_values_title_en?.value || '',
    about_fast_values_subtitle_id: configs.about_fast_values_subtitle_id?.value || '',
    about_fast_values_subtitle_en: configs.about_fast_values_subtitle_en?.value || '',
    about_fast_values_items: configs.about_fast_values_items?.value || [{ id: 1, title_id: '', title_en: '', description_id: '', description_en: '', icon: '', order: 1 }],
    
    // I.D.C Values
    about_idc_values_title_id: configs.about_idc_values_title_id?.value || '',
    about_idc_values_title_en: configs.about_idc_values_title_en?.value || '',
    about_idc_values_subtitle_id: configs.about_idc_values_subtitle_id?.value || '',
    about_idc_values_subtitle_en: configs.about_idc_values_subtitle_en?.value || '',
    about_idc_values_items: configs.about_idc_values_items?.value || [{ id: 1, title_id: '', title_en: '', description_id: '', description_en: '', icon: '', order: 1 }],
    
    // Closing Statement
    about_closing_statement_id: configs.about_closing_statement_id?.value || '',
    about_closing_statement_en: configs.about_closing_statement_en?.value || '',
  }
}

const form = reactive(initializeFormData())

// File upload refs
const ourStoryImageFile = ref(null)
const logoPhilosophyImageFile = ref(null)
const fastValueIconFiles = ref({})
const idcValueIconFiles = ref({})

// Image preview URLs (computed from configurations)
const ourStoryImagePreview = ref('')
const logoPhilosophyImagePreview = ref('')
const fastValueIconPreviews = ref({})
const idcValueIconPreviews = ref({})

// Helper function to convert storage path to full URL
const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('data:') || path.startsWith('http://') || path.startsWith('https://')) {
    return path
  }
  // Add cache busting timestamp
  const timestamp = new Date().getTime()
  return `/storage/${path}?t=${timestamp}`
}

// Watch for configuration changes and update form
watch(() => props.configurations, (newConfigs) => {
  const newFormData = initializeFormData()
  Object.assign(form, newFormData)
  
  // Update image previews from server data
  ourStoryImagePreview.value = getImageUrl(newConfigs.about_our_story_image?.value || '')
  logoPhilosophyImagePreview.value = getImageUrl(newConfigs.about_logo_philosophy_image?.value || '')
  
  // Clear icon previews and set from server data if exists
  fastValueIconPreviews.value = {}
  idcValueIconPreviews.value = {}
  
  // Keep icon paths in form data for server consistency, but don't convert to full URLs
  // The template will handle URL conversion via getImageUrl() function
}, { deep: true, immediate: true })

const saveSettings = () => {
  console.log('🔧 Starting saveSettings...')
  console.log('📁 fastValueIconFiles:', fastValueIconFiles.value)
  console.log('📁 idcValueIconFiles:', idcValueIconFiles.value)
  
  const changes = []
  
  // Ensure proper order for array items
  if (form.about_mission_items && Array.isArray(form.about_mission_items)) {
    form.about_mission_items.forEach((item, index) => {
      item.order = index + 1
    })
  }
  
  if (form.about_logo_philosophy_points && Array.isArray(form.about_logo_philosophy_points)) {
    form.about_logo_philosophy_points.forEach((point, index) => {
      point.order = index + 1
    })
  }
  
  if (form.about_fast_values_items && Array.isArray(form.about_fast_values_items)) {
    form.about_fast_values_items.forEach((item, index) => {
      item.order = index + 1
      // Remove base64 data from icon field if it exists - file uploads handle this separately
      if (item.icon && item.icon.startsWith('data:')) {
        item.icon = '' // Clear base64, file upload will set the correct path
      }
    })
  }
  
  if (form.about_idc_values_items && Array.isArray(form.about_idc_values_items)) {
    form.about_idc_values_items.forEach((item, index) => {
      item.order = index + 1
      // Remove base64 data from icon field if it exists - file uploads handle this separately
      if (item.icon && item.icon.startsWith('data:')) {
        item.icon = '' // Clear base64, file upload will set the correct path
      }
    })
  }
  
  // Check for changes and collect them (exclude image keys since they will be handled separately)
  Object.keys(form).forEach(key => {
    // Skip image fields as they will be handled as file uploads
    if (key.includes('image')) {
      return
    }
    
    const value = form[key]
    let type = 'string'
    
    if (key.includes('content') || key.includes('statement') || key.includes('subtitle')) {
      type = 'text'
    } else if (key === 'about_mission_items' || key === 'about_logo_philosophy_points' || key === 'about_fast_values_items' || key === 'about_idc_values_items') {
      type = 'json'
    }
    
    // Always add the change regardless of comparison
    changes.push({
      key,
      value,
      type
    })
  })
  
  // Handle file uploads separately
  if (ourStoryImageFile.value) {
    changes.push({
      key: 'about_our_story_image',
      value: ourStoryImageFile.value,
      type: 'file'
    })
  }
  
  if (logoPhilosophyImageFile.value) {
    changes.push({
      key: 'about_logo_philosophy_image',
      value: logoPhilosophyImageFile.value,
      type: 'file'
    })
  }
  
  // Handle icon file uploads for F.A.S.T Values
  console.log('📤 Processing F.A.S.T icon files...')
  Object.keys(fastValueIconFiles.value).forEach(index => {
    const file = fastValueIconFiles.value[index]
    console.log(`📤 F.A.S.T icon ${index}:`, file)
    if (file) {
      const change = {
        key: `about_fast_values_icon_${index}`,
        value: file,
        type: 'file',
        metadata: {
          arrayKey: 'about_fast_values_items',
          arrayIndex: parseInt(index),
          arrayField: 'icon'
        }
      }
      console.log('📤 Adding F.A.S.T icon change:', change)
      changes.push(change)
    }
  })
  
  // Handle icon file uploads for I.D.C Values
  console.log('📤 Processing I.D.C icon files...')
  Object.keys(idcValueIconFiles.value).forEach(index => {
    const file = idcValueIconFiles.value[index]
    console.log(`📤 I.D.C icon ${index}:`, file)
    if (file) {
      const change = {
        key: `about_idc_values_icon_${index}`,
        value: file,
        type: 'file',
        metadata: {
          arrayKey: 'about_idc_values_items',
          arrayIndex: parseInt(index),
          arrayField: 'icon'
        }
      }
      console.log('📤 Adding I.D.C icon change:', change)
      changes.push(change)
    }
  })
  
  // About changes detected
  
  console.log('📋 Total changes:', changes.length)
  console.log('📋 All changes:', changes)
  
  if (changes.length > 0) {
    console.log('🚀 Emitting bulkSave...')
    emit('bulkSave', 'about', changes)
    
    // Clear file refs after save initiation
    ourStoryImageFile.value = null
    logoPhilosophyImageFile.value = null
    fastValueIconFiles.value = {}
    idcValueIconFiles.value = {}
    fastValueIconPreviews.value = {}
    idcValueIconPreviews.value = {}
  }
}

const addMissionItem = () => {
  if (!form.about_mission_items) {
    form.about_mission_items = []
  }
  const newId = Math.max(...form.about_mission_items.map(item => item.id || 0), 0) + 1
  form.about_mission_items.push({
    id: newId,
    text_id: '',
    text_en: '',
    order: form.about_mission_items.length + 1
  })
}

const removeMissionItem = (index) => {
  if (form.about_mission_items && form.about_mission_items.length > index) {
    form.about_mission_items.splice(index, 1)
  }
}

const addPhilosophyPoint = () => {
  if (!form.about_logo_philosophy_points) {
    form.about_logo_philosophy_points = []
  }
  const newId = Math.max(...form.about_logo_philosophy_points.map(point => point.id || 0), 0) + 1
  form.about_logo_philosophy_points.push({
    id: newId,
    text_id: '',
    text_en: '',
    order: form.about_logo_philosophy_points.length + 1
  })
}

const removePhilosophyPoint = (index) => {
  if (form.about_logo_philosophy_points && form.about_logo_philosophy_points.length > index) {
    form.about_logo_philosophy_points.splice(index, 1)
  }
}

const handleOurStoryImageUpload = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    ourStoryImageFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      // Update preview only, don't modify form data
      ourStoryImagePreview.value = e.target?.result
    }
    reader.readAsDataURL(file)
  }
}

const handleLogoPhilosophyImageUpload = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    logoPhilosophyImageFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      // Update preview only, don't modify form data
      logoPhilosophyImagePreview.value = e.target?.result
    }
    reader.readAsDataURL(file)
  }
}

// F.A.S.T Values methods
const addFastValue = () => {
  if (!form.about_fast_values_items) {
    form.about_fast_values_items = []
  }
  const newId = Math.max(...form.about_fast_values_items.map(item => item.id || 0), 0) + 1
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
  if (form.about_fast_values_items && form.about_fast_values_items.length > index) {
    form.about_fast_values_items.splice(index, 1)
    // Clear file reference and preview for this index
    delete fastValueIconFiles.value[index]
    delete fastValueIconPreviews.value[index]
  }
}

// I.D.C Values methods
const addIdcValue = () => {
  if (!form.about_idc_values_items) {
    form.about_idc_values_items = []
  }
  const newId = Math.max(...form.about_idc_values_items.map(item => item.id || 0), 0) + 1
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
  if (form.about_idc_values_items && form.about_idc_values_items.length > index) {
    form.about_idc_values_items.splice(index, 1)
    // Clear file reference and preview for this index
    delete idcValueIconFiles.value[index]
    delete idcValueIconPreviews.value[index]
  }
}

// F.A.S.T Values icon upload handlers
const handleFastValueIconUpload = (event, index) => {
  console.log('🖼️ F.A.S.T icon upload triggered for index:', index)
  const file = event.target.files?.[0]
  console.log('🖼️ File selected:', file)
  
  if (file) {
    // Store file reference for upload
    fastValueIconFiles.value[index] = file
    console.log('🖼️ Stored file in fastValueIconFiles[' + index + ']:', file)
    
    // Create preview URL (separate from form data)
    const reader = new FileReader()
    reader.onload = (e) => {
      fastValueIconPreviews.value[index] = e.target?.result
      console.log('🖼️ Preview set for F.A.S.T index', index)
    }
    reader.readAsDataURL(file)
  }
}

const clearFastValueIcon = (index) => {
  if (form.about_fast_values_items[index]) {
    form.about_fast_values_items[index].icon = ''
    // Clear file reference and preview
    delete fastValueIconFiles.value[index]
    delete fastValueIconPreviews.value[index]
  }
}

// I.D.C Values icon upload handlers
const handleIdcValueIconUpload = (event, index) => {
  const file = event.target.files?.[0]
  if (file) {
    // Store file reference for upload
    idcValueIconFiles.value[index] = file
    
    // Create preview URL (separate from form data)
    const reader = new FileReader()
    reader.onload = (e) => {
      idcValueIconPreviews.value[index] = e.target?.result
    }
    reader.readAsDataURL(file)
  }
}

const clearIdcValueIcon = (index) => {
  if (form.about_idc_values_items[index]) {
    form.about_idc_values_items[index].icon = ''
    // Clear file reference and preview
    delete idcValueIconFiles.value[index]
    delete idcValueIconPreviews.value[index]
  }
}
</script>