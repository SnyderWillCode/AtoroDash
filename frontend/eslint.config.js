import pluginVue from 'eslint-plugin-vue'
import vueTsEslintConfig from '@vue/eslint-config-typescript'
import skipFormatting from '@vue/eslint-config-prettier/skip-formatting'

export default [
	{
		name: 'app/files-to-lint',
		files: ['**/*.{ts,mts,tsx,vue}'],
	},

	{
		name: 'app/files-to-ignore',
		ignores: ['**/dist/**', '**/dist-ssr/**', '**/coverage/**', 'env.d.ts'],
	},

	...pluginVue.configs['flat/essential'],
	...vueTsEslintConfig(),
	skipFormatting,
	{
		rules: {
			'vue/multi-word-component-names': 'off',
		},
	},
]
