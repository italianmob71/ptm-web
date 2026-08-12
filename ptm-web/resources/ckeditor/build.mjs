import * as esbuild from 'esbuild';

await esbuild.build({
    entryPoints: ['src/ptm-editor.js'],
    bundle: true,
    minify: true,
    sourcemap: false,
    format: 'iife',
    globalName: 'PTMEditor',
    outfile: '../../public/js/ptm-editor.js',
    loader: { '.svg': 'text' },
    define: {
        'process.env.NODE_ENV': '"production"'
    }
});

console.log('Build complete: public/js/ptm-editor.js');
