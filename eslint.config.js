import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import prettier from 'eslint-config-prettier';

export default [
    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    prettier,
    {
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                route: 'readonly', // Ziggy route helper
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                process: 'readonly',
            },
        },
        rules: {
            // Vue-specific rules
            'vue/multi-word-component-names': 'off',
            'vue/require-default-prop': 'warn',
            'vue/prefer-import-from-vue': 'error',

            // General JavaScript rules
            'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
            'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
            'no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
        },
    },
    {
        ignores: ['vendor/**', 'node_modules/**', 'public/build/**', 'bootstrap/cache/**'],
    },
];
