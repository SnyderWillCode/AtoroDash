
## Frontend Development Requirements
### General Guidelines

#### Framework
- Use **Vue 3** as the frontend framework

#### Styling
- Use **Tailwind CSS** for all styling needs
- Adhere to a mobile-first design philosophy

#### Internationalization
- Use **vue-i18n** plugin for translation and localization

#### Accessibility
- Ensure compliance with **WCAG 2.1 AA** standards

### Component Handling

#### Reusability
Before creating a new Vue component:
1. Scan the `/frontend` folders to check if a component already exists
2. If it does not exist:
   - Create a new component
   - Design it to be modular and scalable
   - Place it in an appropriate subfolder (e.g., `/frontend/components`)

#### Component Style Conventions
- Use Tailwind CSS classes for styling
- Add accessibility attributes (e.g., `aria-label`, `role`)
- Include prop validation for all props

### Key Components

#### Form Components
- **InputField** with validation and Tailwind styling
- **Button** with hover/active states

#### Layouts
- **DashboardLayout** for admin or user panels
- **Card** for displaying concise information
- **Modals** for confirmation dialogs or displaying important messages
- **Tables** for data display, with pagination and responsive design

### State Management
- Use the **Vue Composition API** for handling state within components

### Frontend Performance
- Optimize assets (images, fonts) and minimize load times
- Implement lazy loading for components and routes
- Ensure smooth transitions and animations using:
  - Vue's Transition API
  - Framer Motion
### Security and Best Practices

#### Backend Security
- Sanitize all inputs to prevent XSS attacks
- Avoid exposing sensitive data in responses

#### Frontend Security
- Validate user input on both the client and server
- Use HTTPS for all requests
- The path is always `/api/<endpoint>` 
- Cors should be respected

## Final Development Guidelines

### Documentation Requirements
- Maintain comprehensive inline code documentation
- Keep README files up-to-date with setup instructions and API documentation
- Document all configuration requirements and environment variables
- Include examples for complex functionality

### Testing Protocol
- Write unit tests for all new features and bug fixes
- Implement end-to-end testing for critical user flows
- Maintain minimum 80% code coverage
- Perform cross-browser compatibility testing
- Test responsive design across multiple devices

### Scalability Standards
- Follow SOLID principles in code design
- Implement caching strategies where appropriate
- Design database schemas with future growth in mind
- Use microservices architecture when beneficial
- Optimize database queries for performance

### Collaboration Guidelines
- Follow PSR-12 coding standards for PHP
- Use ESLint and Prettier for JavaScript/Vue code formatting
- Maintain clear Git commit messages following conventional commits
- Review pull requests thoroughly with detailed feedback
- Keep dependencies updated and security patches applied