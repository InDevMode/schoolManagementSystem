<template>
    <div>
        <div
            class="rounded-xl border overflow-hidden"
            :class="[
                error
                    ? 'border-danger-500 focus-within:ring-2 focus-within:ring-danger-500'
                    : 'border-gray-300 dark:border-gray-600 focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-transparent',
            ]"
        >
            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">

                <!-- Bold -->
                <button type="button" @click="editor?.chain().focus().toggleBold().run()" :class="btnClass(editor?.isActive('bold'))" title="Gras">
                    <span class="font-bold text-sm leading-none">B</span>
                </button>

                <!-- Italic -->
                <button type="button" @click="editor?.chain().focus().toggleItalic().run()" :class="btnClass(editor?.isActive('italic'))" title="Italique">
                    <span class="italic text-sm leading-none">I</span>
                </button>

                <!-- Underline -->
                <button type="button" @click="editor?.chain().focus().toggleUnderline().run()" :class="btnClass(editor?.isActive('underline'))" title="Souligné">
                    <span class="underline text-sm leading-none">U</span>
                </button>

                <!-- Strike -->
                <button type="button" @click="editor?.chain().focus().toggleStrike().run()" :class="btnClass(editor?.isActive('strike'))" title="Barré">
                    <span class="line-through text-sm leading-none">S</span>
                </button>

                <div class="w-px h-5 bg-gray-300 dark:bg-gray-500 mx-1" />

                <!-- H1 -->
                <button type="button" @click="editor?.chain().focus().toggleHeading({ level: 1 }).run()" :class="btnClass(editor?.isActive('heading', { level: 1 }))" title="Titre 1">
                    <span class="text-xs font-bold leading-none">H1</span>
                </button>

                <!-- H2 -->
                <button type="button" @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()" :class="btnClass(editor?.isActive('heading', { level: 2 }))" title="Titre 2">
                    <span class="text-xs font-bold leading-none">H2</span>
                </button>

                <!-- H3 -->
                <button type="button" @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()" :class="btnClass(editor?.isActive('heading', { level: 3 }))" title="Titre 3">
                    <span class="text-xs font-bold leading-none">H3</span>
                </button>

                <div class="w-px h-5 bg-gray-300 dark:bg-gray-500 mx-1" />

                <!-- Bullet list -->
                <button type="button" @click="editor?.chain().focus().toggleBulletList().run()" :class="btnClass(editor?.isActive('bulletList'))" title="Liste à puces">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="4" cy="6" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="4" cy="18" r="1.5" fill="currentColor" stroke="none"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13" />
                    </svg>
                </button>

                <!-- Ordered list -->
                <button type="button" @click="editor?.chain().focus().toggleOrderedList().run()" :class="btnClass(editor?.isActive('orderedList'))" title="Liste numérotée">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13" />
                        <text x="1" y="7" font-size="5" fill="currentColor" stroke="none">1.</text>
                        <text x="1" y="13" font-size="5" fill="currentColor" stroke="none">2.</text>
                        <text x="1" y="19" font-size="5" fill="currentColor" stroke="none">3.</text>
                    </svg>
                </button>

                <div class="w-px h-5 bg-gray-300 dark:bg-gray-500 mx-1" />

                <!-- Text color -->
                <label class="relative cursor-pointer flex items-center justify-center w-7 h-7 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Couleur du texte">
                    <span
                        class="w-4 h-4 rounded border border-gray-400 dark:border-gray-500 block"
                        :style="{ backgroundColor: currentColor }"
                    />
                    <input
                        type="color"
                        class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                        :value="currentColor"
                        @input="(e) => setColor((e.target as HTMLInputElement).value)"
                    />
                </label>

                <!-- Link -->
                <button type="button" @click="setLink" :class="btnClass(editor?.isActive('link'))" title="Lien">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </button>

                <div class="w-px h-5 bg-gray-300 dark:bg-gray-500 mx-1" />

                <!-- Code block -->
                <button type="button" @click="editor?.chain().focus().toggleCodeBlock().run()" :class="btnClass(editor?.isActive('codeBlock'))" title="Bloc de code">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </button>

                <!-- Inline code -->
                <button type="button" @click="editor?.chain().focus().toggleCode().run()" :class="btnClass(editor?.isActive('code'))" title="Code inline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>

            <!-- Editor content -->
            <EditorContent
                :editor="editor"
                class="min-h-[160px] px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 [&_.tiptap]:outline-none [&_.tiptap]:min-h-[140px] [&_.tiptap_h1]:text-2xl [&_.tiptap_h1]:font-bold [&_.tiptap_h2]:text-xl [&_.tiptap_h2]:font-bold [&_.tiptap_h3]:text-lg [&_.tiptap_h3]:font-semibold [&_.tiptap_ul]:list-disc [&_.tiptap_ul]:pl-5 [&_.tiptap_ol]:list-decimal [&_.tiptap_ol]:pl-5 [&_.tiptap_code]:bg-gray-100 [&_.tiptap_code]:dark:bg-gray-700 [&_.tiptap_code]:px-1 [&_.tiptap_code]:rounded [&_.tiptap_pre]:bg-gray-100 [&_.tiptap_pre]:dark:bg-gray-700 [&_.tiptap_pre]:p-3 [&_.tiptap_pre]:rounded-xl [&_.tiptap_a]:text-primary-600 [&_.tiptap_a]:underline"
            />
        </div>
        <p v-if="error" class="mt-1 text-xs text-danger-600">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { watch, onBeforeUnmount, computed } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Color from '@tiptap/extension-color';
import { TextStyle } from '@tiptap/extension-text-style';
import CodeBlock from '@tiptap/extension-code-block';

// ── Props / emits ───────────────────────────────────────────────────────────
const props = defineProps<{
    modelValue: string;
    error?: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

// ── Toolbar button class helper ─────────────────────────────────────────────
function btnClass(active?: boolean) {
    return [
        'flex items-center justify-center w-7 h-7 rounded-xl transition-colors text-gray-700 dark:text-gray-300 flex-shrink-0',
        active
            ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300'
            : 'hover:bg-gray-200 dark:hover:bg-gray-600',
    ];
}

// ── Editor setup ────────────────────────────────────────────────────────────
const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Underline,
        TextStyle,
        Color,
        CodeBlock,
        Link.configure({
            openOnClick: false,
            HTMLAttributes: { class: 'text-primary-600 underline' },
        }),
    ],
    editorProps: {
        attributes: {
            class: 'focus:outline-none',
        },
    },
    onUpdate({ editor }) {
        emit('update:modelValue', editor.getHTML());
    },
});

// Sync external v-model changes into the editor
watch(
    () => props.modelValue,
    (val) => {
        if (editor.value && editor.value.getHTML() !== val) {
            editor.value.commands.setContent(val, false);
        }
    },
);

onBeforeUnmount(() => editor.value?.destroy());

// ── Helpers ─────────────────────────────────────────────────────────────────
const currentColor = computed(
    () => editor.value?.getAttributes('textStyle').color ?? '#000000',
);

function setColor(color: string) {
    editor.value?.chain().focus().setColor(color).run();
}

function setLink() {
    const prev = editor.value?.getAttributes('link').href ?? '';
    const url = window.prompt('URL du lien :', prev);
    if (url === null) return;
    if (url === '') {
        editor.value?.chain().focus().extendMarkRange('link').unsetLink().run();
    } else {
        editor.value?.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
    }
}
</script>
