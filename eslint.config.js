import js from '@eslint/js';

export default [
    {
        ignores: [
            'bootstrap/cache/**',
            'commitlint.config.cjs',
            'node_modules/**',
            'public/build/**',
            'storage/**',
            'vendor/**',
        ],
    },
    js.configs.recommended,
    {
        files: ['**/*.js', '**/*.cjs', '**/*.mjs'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                Alpine: 'readonly',
                clearTimeout: 'readonly',
                console: 'readonly',
                document: 'readonly',
                navigator: 'readonly',
                requestAnimationFrame: 'readonly',
                setTimeout: 'readonly',
                window: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
        },
    },
];
