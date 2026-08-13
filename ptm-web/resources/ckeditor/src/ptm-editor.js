// Custom CKEditor 5 Classic build for PTM
// Includes: alignment, strikethrough, subscript, superscript, removeFormat, indent,
//           image picker (custom), pdf picker (custom)

import {
    ClassicEditor,
    Autoformat,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Subscript,
    Superscript,
    Alignment,
    BlockQuote,
    Heading,
    Indent,
    IndentBlock,
    Link,
    List,
    Paragraph,
    Table,
    TableToolbar,
    HorizontalLine,
    RemoveFormat,
    Undo,
    Clipboard,
    Enter,
    ShiftEnter,
    Typing,
    Image,
    ImageCaption,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    CKFinder,
    FileRepository
} from 'ckeditor5';

import ImagePicker from './imagepicker.js';
import PdfPicker from './pdfpicker.js';

class PTMEditor extends ClassicEditor {}
PTMEditor.builtinPlugins = [
    Autoformat,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Subscript,
    Superscript,
    Alignment,
    BlockQuote,
    Heading,
    Indent,
    IndentBlock,
    Link,
    List,
    Paragraph,
    Table,
    TableToolbar,
    HorizontalLine,
    RemoveFormat,
    Undo,
    Clipboard,
    Enter,
    ShiftEnter,
    Typing,
    Image,
    ImageCaption,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    CKFinder,
    FileRepository,
    ImagePicker,
    PdfPicker
];

PTMEditor.defaultConfig = {
    toolbar: {
        items: [
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'subscript',
            'superscript',
            '|',
            'alignment',
            '|',
            'link',
            'bulletedList',
            'numberedList',
            'outdent',
            'indent',
            '|',
            'blockQuote',
            'insertTable',
            'horizontalLine',
            'removeFormat',
            '-',
            'imagePicker',
            'pdfPicker',
            '|',
            'undo',
            'redo'
        ],
        shouldNotGroupWhenFull: true
    },
    alignment: {
        options: ['left', 'center', 'right', 'justify']
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
        ]
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
    },
    image: {
        toolbar: ['imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight']
    },
    licenseKey: 'GPL',
    language: 'en'
};

export default PTMEditor;
