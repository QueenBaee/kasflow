# Kasflow Frontend Documentation

## 🎨 UI/UX Architecture

### Design Philosophy

**Speed Over Beauty** - Every design decision prioritizes transaction speed for grocery store workers.

### Key UX Decisions Explained

#### 1. **Cashier UI: Minimal Steps**
**Why:** Cashiers need to record sales quickly during busy hours.

**Implementation:**
- Full-screen numeric input
- Auto-focus on amount field
- One-tap submit
- Success animation (1.5s) then auto-redirect
- Optional note field (collapsed by default)

**Real-world scenario:**
```
Customer buys items → Cashier calculates total
→ Opens app → Amount auto-focused
→ Types 50000 → Taps "Save Income"
→ Success animation → Back to home
Total time: ~5 seconds
```

#### 2. **Large Touch Targets (48px minimum)**
**Why:** Mobile users need easy tapping, especially in busy environments.

**Implementation:**
- Buttons: min-height 44px (md), 60px (xl)
- Bottom navigation: 64px height
- Input fields: 48px height
- Tap areas include padding

#### 3. **Currency Auto-Formatting**
**Why:** Reduces cognitive load, prevents input errors.

**Implementation:**
```javascript
Input: 50000
Display: 50.000 (Indonesian format)
Storage: 50000 (numeric)
```

#### 4. **Bottom Navigation for Cashier**
**Why:** Thumb-friendly on mobile, always accessible.

**Implementation:**
- Fixed position
- 2 main actions only
- Large icons (32px)
- Active state highlighting

#### 5. **Owner Sidebar Navigation**
**Why:** Desktop-first for owners who analyze data on larger screens.

**Implementation:**
- Collapsible on mobile
- Store selector at top
- Grouped by function
- Visual active states

## 📁 Folder Structure

```
resources/js/
├── Layouts/
│   ├── OwnerLayout.vue       # Sidebar, store selector
│   └── CashierLayout.vue     # Minimal header, bottom nav
├── Pages/
│   ├── Auth/
│   │   ├── Login.vue
│   │   └── Register.vue
│   ├── Owner/
│   │   ├── Dashboard.vue     # Summary stats, quick actions
│   │   ├── Expenses.vue      # (To be created)
│   │   └── Reports.vue       # (To be created)
│   └── Cashier/
│       ├── Index.vue         # Today's income, quick action
│       └── Income.vue        # Large numeric input
├── Components/
│   ├── Toast.vue             # Flash notifications
│   ├── BaseButton.vue        # Reusable button with variants
│   ├── CurrencyInput.vue     # Auto-formatting input
│   └── SummaryStatCard.vue   # Dashboard metrics
├── Composables/
│   ├── useAuth.js            # Auth state management
│   └── useToast.js           # Toast notifications
└── Utils/
    └── currency.js           # Currency formatting
```

## 🎯 Component Architecture

### Base Components

#### BaseButton
**Props:**
- `variant`: primary, secondary, danger, success
- `size`: sm, md, lg, xl
- `loading`: boolean
- `disabled`: boolean

**Usage:**
```vue
<BaseButton variant="success" size="xl" :loading="form.processing">
    Save Income
</BaseButton>
```

#### CurrencyInput
**Features:**
- Auto-format on input
- Numeric keyboard on mobile
- Large font (text-2xl)
- Auto-select on focus

**Props:**
- `modelValue`: number
- `label`: string
- `error`: string
- `disabled`: boolean

#### SummaryStatCard
**Features:**
- Color-coded by type (income/expense/profit)
- Icon support
- Formatted values
- Subtitle text

**Props:**
- `label`: string
- `value`: number
- `type`: income | expense | profit | default
- `subtitle`: string
- `icon`: component

### Layout Components

#### OwnerLayout
**Features:**
- Responsive sidebar (collapsible on mobile)
- Store selector dropdown
- User profile menu
- Navigation with active states
- Logout button

**Props:**
- `stores`: array
- `currentStore`: object

#### CashierLayout
**Features:**
- Minimal header with store name
- Current date display
- Logout button
- Fixed bottom navigation
- Large touch targets

**Props:**
- `storeName`: string

## 🎨 Design System

### Color Semantics

```css
Green (#10b981) = Income / Success
Red (#ef4444) = Expense / Danger
Blue (#3b82f6) = Neutral / Info
Gray = UI elements
```

### Typography Scale

```css
text-xs: 0.75rem (12px)
text-sm: 0.875rem (14px)
text-base: 1rem (16px)
text-lg: 1.125rem (18px)
text-xl: 1.25rem (20px)
text-2xl: 1.5rem (24px) - Currency input
text-3xl: 1.875rem (30px) - Summary values
text-5xl: 3rem (48px) - Today's income
```

### Spacing System

```css
Padding: p-4 (16px), p-6 (24px), p-8 (32px)
Gap: gap-4 (16px), gap-6 (24px)
Rounded: rounded-lg (8px), rounded-2xl (16px)
```

## 🔄 State Management

### Composables Pattern

**useAuth**
```javascript
const { user, isAuthenticated, logout } = useAuth();
```

**useToast**
```javascript
const { toast, showToast } = useToast();
showToast('Success!', 'success');
```

### Form Handling (Inertia)
```javascript
const form = useForm({
    amount: 0,
    note: '',
});

form.post('/api/endpoint', {
    onSuccess: () => form.reset(),
});
```

## 📱 Mobile Responsiveness

### Breakpoints
```css
sm: 640px
md: 768px
lg: 1024px
xl: 1280px
```

### Mobile-First Classes
```vue
<!-- Mobile: full width, Desktop: half width -->
<div class="w-full md:w-1/2">

<!-- Mobile: stacked, Desktop: grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
```

### Touch Optimization
- Minimum tap target: 48x48px
- Prevent zoom on input focus
- Large buttons on mobile
- Bottom navigation for thumb reach

## ⚡ Performance Optimizations

### Lazy Loading
```javascript
// Inertia automatically lazy loads pages
const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
```

### Component Reuse
- BaseButton used everywhere
- CurrencyInput for all money inputs
- SummaryStatCard for all metrics

### Debouncing (Future)
```javascript
import { debounce } from 'lodash';
const search = debounce((query) => {
    // Search logic
}, 300);
```

## 🎭 Animation Guidelines

### Subtle Transitions
```css
transition-colors: 150ms
transition-transform: 300ms
hover:scale-105: Slight scale on hover
```

### Success Animation
```vue
<Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 scale-90"
    enter-to-class="opacity-100 scale-100"
>
    <div v-if="showSuccess">Success!</div>
</Transition>
```

## 🔐 Role-Based Rendering

### Route Guards (Future)
```javascript
// Middleware to check role
router.beforeEach((to, from, next) => {
    if (to.meta.requiresOwner && !user.isOwner) {
        next('/cashier');
    } else {
        next();
    }
});
```

### Conditional UI
```vue
<div v-if="user.role === 'owner'">
    <!-- Owner-only content -->
</div>
```

## 🚀 Future Enhancements

### PWA Support
```javascript
// Add to vite.config.js
import { VitePWA } from 'vite-plugin-pwa';

plugins: [
    VitePWA({
        registerType: 'autoUpdate',
        manifest: {
            name: 'Kasflow',
            short_name: 'Kasflow',
            theme_color: '#10b981',
        },
    }),
],
```

### Offline Support
```javascript
// Service worker for offline transactions
self.addEventListener('fetch', (event) => {
    // Cache-first strategy
});
```

### Chart Integration
```bash
npm install chart.js vue-chartjs
```

```vue
<template>
    <Line :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { Line } from 'vue-chartjs';
</script>
```

## 🧪 Testing Strategy

### Component Tests (Future)
```javascript
import { mount } from '@vue/test-utils';
import CurrencyInput from './CurrencyInput.vue';

test('formats currency correctly', () => {
    const wrapper = mount(CurrencyInput, {
        props: { modelValue: 50000 },
    });
    expect(wrapper.text()).toContain('50.000');
});
```

## 📊 Real-World Cashier Workflow

### Morning Shift Start
1. Cashier opens app on phone
2. Auto-login (session persisted)
3. Sees today's income: Rp 0
4. Ready to record first sale

### During Rush Hour
1. Customer: "Rp 45.000"
2. Cashier taps "Add Income"
3. Types: 45000 (auto-formatted to 45.000)
4. Taps "Save Income"
5. Success animation (1.5s)
6. Back to home, sees updated total
7. **Total time: 5 seconds**

### End of Day
1. Cashier sees total: Rp 2.450.000
2. 87 transactions recorded
3. Logs out

## 🎓 Best Practices Applied

### 1. **Composition API**
- Cleaner code
- Better TypeScript support
- Reusable logic via composables

### 2. **Inertia.js Benefits**
- No API endpoints needed for pages
- Server-side routing
- Automatic CSRF protection
- Flash messages built-in

### 3. **TailwindCSS Utility-First**
- Rapid development
- Consistent design
- Small bundle size (purged)
- Mobile-first responsive

### 4. **Accessibility**
- Semantic HTML
- ARIA labels
- Keyboard navigation
- High contrast text

## 🔧 Development Commands

```bash
# Install dependencies
npm install

# Start dev server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## 🌐 Browser Support

- Chrome/Edge: Latest 2 versions
- Firefox: Latest 2 versions
- Safari: Latest 2 versions
- Mobile Safari: iOS 12+
- Chrome Mobile: Latest

## 📝 Code Style

### Vue SFC Order
```vue
<template>
    <!-- HTML -->
</template>

<script setup>
// Imports
// Props
// Composables
// Reactive state
// Computed
// Methods
// Lifecycle hooks
</script>

<style scoped>
/* Component-specific styles (rare with Tailwind) */
</style>
```

### Naming Conventions
- Components: PascalCase (BaseButton.vue)
- Composables: camelCase with 'use' prefix (useAuth.js)
- Props: camelCase
- Events: kebab-case

## 🎯 Performance Metrics Goals

- First Contentful Paint: < 1.5s
- Time to Interactive: < 3s
- Lighthouse Score: > 90
- Bundle Size: < 200KB (gzipped)

## 🔍 Debugging Tips

### Vue Devtools
```bash
# Install browser extension
# Inspect components, state, events
```

### Inertia Debugging
```javascript
// Check page props
console.log(usePage().props);

// Check current URL
console.log(usePage().url);
```

### Network Tab
- Check Inertia requests (X-Inertia header)
- Verify form submissions
- Monitor response times
