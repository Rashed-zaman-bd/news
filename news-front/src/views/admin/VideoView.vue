<!-- views/admin/VideoIndex.vue -->

<template>
  <div class="p-6 max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Videos</h1>
      <button
        @click="openCreate"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition"
      >
        + Add Video
      </button>
    </div>

    <div v-if="loading" class="text-gray-500">Loading...</div>

    <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
          <tr>
            <th class="px-4 py-3">Thumbnail</th>
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="video in videos" :key="video.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3">
              <img
                :src="video.thumbnail ?? '/placeholder.png'"
                :alt="video.title"
                class="w-20 h-12 object-cover rounded"
              />
            </td>
            <td class="px-4 py-3 font-medium text-gray-800">{{ video.title }}</td>
            <td class="px-4 py-3 capitalize text-gray-500">{{ video.video_type }}</td>
            <td class="px-4 py-3">
              <span
                :class="video.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                class="px-2 py-1 rounded-full text-xs font-medium"
              >
                {{ video.is_active ? "Active" : "Inactive" }}
              </span>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <button @click="openEdit(video)" class="text-indigo-600 hover:underline text-sm">
                Edit
              </button>
              <button @click="deleteVideo(video)" class="text-red-600 hover:underline text-sm">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="videos.length === 0">
            <td colspan="5" class="px-4 py-6 text-center text-gray-400">No videos yet.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create / Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">
          {{ editingSlug ? "Edit Video" : "Add Video" }}
        </h2>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input v-model="form.title" type="text" class="w-full border rounded-lg px-3 py-2" required />
            <p v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Thumbnail {{ editingSlug ? "(leave blank to keep current)" : "" }}
            </label>
            <input type="file" accept="image/*" @change="onThumbnailChange"
                   class="w-full border rounded-lg px-3 py-2" :required="!editingSlug" />
            <img v-if="thumbnailPreview" :src="thumbnailPreview" class="mt-2 w-32 h-20 object-cover rounded" />
            <p v-if="errors.thumbnail" class="text-red-500 text-xs mt-1">{{ errors.thumbnail[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Video Source</label>
            <div class="flex gap-4 text-sm">
              <label class="flex items-center gap-1">
                <input type="radio" value="upload" v-model="form.video_type" /> Upload MP4
              </label>
              <label class="flex items-center gap-1">
                <input type="radio" value="embed" v-model="form.video_type" /> External Link
              </label>
            </div>
          </div>

          <div v-if="form.video_type === 'upload'">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Video File {{ editingSlug ? "(leave blank to keep current)" : "" }}
            </label>
            <input type="file" accept="video/mp4,video/quicktime,video/ogg" @change="onVideoFileChange"
                   class="w-full border rounded-lg px-3 py-2" />
            <p v-if="errors.video" class="text-red-500 text-xs mt-1">{{ errors.video[0] }}</p>
          </div>

          <div v-else>
            <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
            <input v-model="form.video_url" type="url" placeholder="https://youtube.com/..."
                   class="w-full border rounded-lg px-3 py-2" />
            <p v-if="errors.video_url" class="text-red-500 text-xs mt-1">{{ errors.video_url[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
          </div>

          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="form.is_active" /> Active
          </label>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showModal = false"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" :disabled="submitting"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50">
              {{ submitting ? "Saving..." : editingSlug ? "Update" : "Create" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import api from "@/services/api";

interface Video {
  id: number;
  title: string;
  slug: string;
  thumbnail: string | null;
  video_type: "upload" | "embed";
  video_url: string | null;
  description: string | null;
  is_active: boolean;
  created_at: string;
}

const videos = ref<Video[]>([]);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const showModal = ref(false);
const editingSlug = ref<string | null>(null);
const submitting = ref(false);

const form = reactive({
  title: "",
  slug: "",
  video_type: "upload" as "upload" | "embed",
  video_url: "",
  description: "",
  is_active: true,
  thumbnailFile: null as File | null,
  videoFile: null as File | null,
});

const thumbnailPreview = ref<string | null>(null);

function resetForm() {
  form.title = "";
  form.slug = "";
  form.video_type = "upload";
  form.video_url = "";
  form.description = "";
  form.is_active = true;
  form.thumbnailFile = null;
  form.videoFile = null;
  thumbnailPreview.value = null;
  errors.value = {};
  editingSlug.value = null;
}

async function fetchVideos() {
  loading.value = true;
  try {
    const { data } = await api.get("/video");
    videos.value = data.data;
  } finally {
    loading.value = false;
  }
}

function openCreate() {
  resetForm();
  showModal.value = true;
}

function openEdit(video: Video) {
  resetForm();
  editingSlug.value = video.slug;
  form.title = video.title;
  form.slug = video.slug;
  form.video_type = video.video_type;
  form.video_url = video.video_type === "embed" ? video.video_url ?? "" : "";
  form.description = video.description ?? "";
  form.is_active = video.is_active;
  thumbnailPreview.value = video.thumbnail;
  showModal.value = true;
}

function onThumbnailChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0] ?? null;
  form.thumbnailFile = file;
  if (file) thumbnailPreview.value = URL.createObjectURL(file);
}

function onVideoFileChange(e: Event) {
  form.videoFile = (e.target as HTMLInputElement).files?.[0] ?? null;
}

function buildFormData(): FormData {
  const fd = new FormData();
  fd.append("title", form.title);
  if (form.slug) fd.append("slug", form.slug);
  fd.append("video_type", form.video_type);
  fd.append("is_active", form.is_active ? "1" : "0");
  if (form.description) fd.append("description", form.description);

  if (form.thumbnailFile) fd.append("thumbnail", form.thumbnailFile);

  if (form.video_type === "upload" && form.videoFile) {
    fd.append("video", form.videoFile);
  }
  if (form.video_type === "embed" && form.video_url) {
    fd.append("video_url", form.video_url);
  }

  return fd;
}

async function submitForm() {
  submitting.value = true;
  errors.value = {};
  try {
    const fd = buildFormData();

    if (editingSlug.value) {
      // Laravel can't parse multipart data on native PUT — spoof the method via POST
      fd.append("_method", "PUT");
      await api.post(`/video/${editingSlug.value}`, fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });
    } else {
      await api.post("/video", fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });
    }

    showModal.value = false;
    resetForm();
    await fetchVideos();
  } catch (err: any) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors;
    } else {
      console.error(err);
      alert("Something went wrong. Please try again.");
    }
  } finally {
    submitting.value = false;
  }
}

async function deleteVideo(video: Video) {
  if (!confirm(`Delete "${video.title}"? This cannot be undone.`)) return;
  try {
    await api.delete(`/video/${video.slug}`);
    videos.value = videos.value.filter((v) => v.id !== video.id);
  } catch (err) {
    console.error(err);
    alert("Failed to delete video.");
  }
}

onMounted(fetchVideos);
</script>