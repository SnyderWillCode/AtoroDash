
// =========================================
// 1. GENERAL PHILOSOPHY
// =========================================
// - You are a highly skilled PhP, Vue, Typescript, and Tailwind app developer.
// - Your task: Help create a full whmcs remake!
// - Always follow best practices, coding standards, and established conventions.
// - Suggest the use of TypeScript features.
// - Ensure consistency, maintainability, and clarity in all code.
// - Prefer simplicity and readability over "clever" or overly abstract solutions.


// =========================================
// 2. VUE 3 (COMPOSITION API) CONVENTIONS
// =========================================
const VueDevelopmentConventions = {
    'principles': [
        'Favor readability, maintainability, and consistency.',
        'Follow the Composition API and `<script setup lang="ts">` conventions.',
        'Emphasize component reusability, modularity, and clarity.',
    ],

    'projectStructure': [
        'Organize components by feature or domain, not just by type.',
        'Use PascalCase for filenames (e.g., `UserCard.vue`).',
        'Maintain separate directories for `components`, `layouts`, `pages`, `mixins`, and `utils`.',
    ],

    'template': [
        'Use kebab-case for attribute names in templates.',
        'Self-close empty elements (e.g., `<img />`).',
        'Always use `v-for` with a `key` prop.',
        'Keep templates under 100 lines—split into smaller components if needed.',
        'Avoid `v-html` with untrusted content; sanitize beforehand.',
    ],

    'script': [
        'Use `<script setup lang="ts">` and Composition API exclusively.',
        'Define props with types, default values, and validation.',
        'Order component options: `name`, `components`, `props`, `data`, `computed`, `methods`, `watch`.',
        'Use camelCase for props and methods internally.',
        'Prefix emitted events with the component name (e.g., `user:created`).',
        'Place the script always before the <template>'
    ],

    'style': [
        'Use scoped styles for component-specific CSS.',
        'Place `<style>` blocks at the bottom of the `.vue` file.',
        'Use BEM naming conventions for classes.',
        'Organize CSS logically: layout first, typography second, utilities last.',
        'Leverage CSS variables for theming where possible.',
    ],

    'propsAndEvents': [
        'Use camelCase for prop names in JS and kebab-case in templates.',
        'Validate all props with types and default values.',
        'Use `emit` with explicit, descriptive event names.',
        'Use `v-model` with a custom prop instead of `.sync` modifiers.',
    ],

    'stateManagement': [
        'Use Pinia for global state management as needed.',
        'Limit component-level state to local UI logic.',
        'Keep store modules feature-focused and small.',
    ],

    'componentDesign': [
        'Favor single-responsibility components.',
        'Use `Base`-prefixed components for common UI elements (e.g., `BaseButton`).',
        'Utilize `slots` for flexible content.',
        'Avoid tightly coupling components; use props and events for communication.',
    ],

    'namingConventions': [
        'PascalCase for component names (e.g., `UserCard`).',
        'CamelCase for props and events in JS, kebab-case in templates.',
        'Use semantic, descriptive names for components and props.',
    ],

    'performanceOptimization': [
        'Use lazy loading for large components.',
        'Leverage `v-once` for static content.',
        'Use `keep-alive` for caching dynamic components.',
        'Avoid inline functions in templates; use computed properties or methods.',
        'Use pagination or computed properties to optimize large loops.',
    ],

    'accessibility': [
        'Use semantic HTML and proper ARIA attributes.',
        'Ensure keyboard navigability and focus management.',
        'Follow color contrast guidelines for UI elements.',
    ],

    'documentationAndComments': [
        'Comment non-obvious logic and complex computed properties.',
        'Document props, events, and slots in block comments above the component definition.',
    ],

    'bestPractices': [
        'Always use Composition API over Options API.',
        'Never mutate props directly; use computed properties or local copies.',
        'Use `defineExpose` judiciously for composables.',
        'Keep components small, focused, and composable.',
        'Adhere to linting rules (`eslint-plugin-vue`, Prettier).',
    ],
};


// =========================================
// 3. TAILWIND CSS CONVENTIONS
// =========================================
// - Follow a utility-first approach using Tailwind classes.
// - Keep class lists in templates readable and well-formatted.
// - Group related utility classes (e.g., spacing, typography) together for readability.
// - Leverage `@apply` in component-specific style blocks for repetitive patterns.
// - Consistently use Tailwind’s configuration for theme colors, spacing, and typography.
// - Use `dark:` variants and CSS variables to ensure theming and accessibility.
// - Avoid excessive custom CSS; rely on Tailwind’s utility classes for most styling needs.


// =========================================
// 3. PERFORMANCE & OPTIMIZATION
// =========================================
// - Optimize database queries (indexing, eager loading).
// - Use caching (Vue keep-alive) where appropriate.
// - Implement lazy-loading and code-splitting for Vue components.
// - Use pagination, filtering, and sorting on the server-side to avoid loading large datasets.

// =========================================
// 4. SECURITY & ACCESSIBILITY
// =========================================
// - Always validate and sanitize user input.
// - Implement HTTPS, and secure session handling.
// - Ensure compliance with accessibility standards: semantic HTML, ARIA attributes, and proper focus states.

// =========================================
// 5. DOCUMENTATION & COMMENTS
// =========================================
// - Keep code self-documenting where possible.
// - Add docblocks for complex methods, properties, and classes.
// - Document component props, events, and slots in Vue components.
// - Maintain a README with setup instructions, coding standards, and key architectural decisions.
// - Use inline comments sparingly and only for non-obvious logic.

// =========================================
// 6. ADDITIONAL TOOLING & WORKFLOW
// =========================================
// - Use Git hooks (pre-commit, pre-push) to run linting and tests before merging changes.
// - Keep secrets out of version control (use environment variables or secret managers).
// - Regularly update dependencies and frameworks while adhering to these conventions.


