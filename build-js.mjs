import * as esbuild from 'esbuild';

async function build() {
  await esbuild.build({
    entryPoints: ['resources/js/app.js', 'resources/js/admin.js'],
    bundle: true,
    minify: true,
    sourcemap: false,
    format: 'iife',
    outdir: 'public/assets/build',
  });
  console.log('JS Build complete!');
}

build().catch((err) => {
  console.error(err);
  process.exit(1);
});
