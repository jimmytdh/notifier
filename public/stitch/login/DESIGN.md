---
name: VitalLink
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#43474f'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#737781'
  outline-variant: '#c3c6d1'
  surface-tint: '#3c5f96'
  primary: '#001c40'
  on-primary: '#ffffff'
  primary-container: '#003166'
  on-primary-container: '#799bd6'
  inverse-primary: '#a9c7ff'
  secondary: '#29695b'
  on-secondary: '#ffffff'
  secondary-container: '#aceddb'
  on-secondary-container: '#2e6d5f'
  tertiary: '#002125'
  on-tertiary: '#ffffff'
  tertiary-container: '#00383e'
  on-tertiary-container: '#67a4ac'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d6e3ff'
  primary-fixed-dim: '#a9c7ff'
  on-primary-fixed: '#001b3d'
  on-primary-fixed-variant: '#21477c'
  secondary-fixed: '#afefdd'
  secondary-fixed-dim: '#93d3c2'
  on-secondary-fixed: '#00201a'
  on-secondary-fixed-variant: '#055043'
  tertiary-fixed: '#afedf6'
  tertiary-fixed-dim: '#93d1da'
  on-tertiary-fixed: '#001f23'
  on-tertiary-fixed-variant: '#004f57'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
  status-critical: '#ba1a1a'
  status-critical-container: '#ffdad6'
  status-new: '#29695b'
  status-info: '#58d7e8'
  surface-active: '#acedda'
  on-surface-active: '#2e6d5f'
  outline-subtle: '#c2c6d2'
typography:
  display-lg:
    fontFamily: Manrope
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
  headline-md:
    fontFamily: Manrope
    fontSize: 20px
    fontWeight: '700'
    lineHeight: 28px
  headline-sm:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-bold:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 14px
  caption-caps:
    fontFamily: Inter
    fontSize: 10px
    fontWeight: '700'
    lineHeight: 12px
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  margin-desktop: 40px
  margin-mobile: 16px
  gutter: 24px
  stack-lg: 24px
  stack-md: 16px
  stack-sm: 8px
  container-max: 1280px
---

## Brand & Style

The brand identity is built on **Reliability, Urgency, and Clinical Precision**. It serves a medical audience (doctors, nursing staff, and hospital administrators) where clarity and speed of information processing are paramount.

The design style is **Corporate / Modern** with a distinct focus on functional hierarchy. It utilizes a structured, high-density layout common in enterprise healthcare SaaS, combining clean Material-inspired surfaces with high-visibility status indicators. The aesthetic is professional and sober, using deep blues to establish trust and vibrant emergency reds to highlight critical patient data.

## Colors

The palette is anchored by **Deep Navy (#003166)** for primary branding and navigation, signifying stability and institutional authority. 

- **Primary:** Used for brand headers, primary actions, and active navigation states.
- **Secondary:** A medical teal used for success states and "Queue" actions.
- **Tertiary:** Reserved for specialized status badges and informational accents.
- **Functional Red:** Essential for the "ER/Trauma" indicators, designed to draw immediate attention without causing fatigue.
- **Surfaces:** A multi-layered neutral system ranging from Pure White (#ffffff) for card bodies to a soft blue-grey (#f7f9fb) for background canvas.

## Typography

The system uses a dual-font approach to balance personality with utility. 

**Manrope** is used for headlines and brand elements to provide a modern, rounded, and welcoming feel that softens the clinical nature of the app. **Inter** is the workhorse for all data-rich areas, chosen for its exceptional legibility at small sizes and its neutral, systematic character.

Key rules:
- All-caps labels with increased tracking (0.05em) are required for secondary metadata like medical department names or timestamps.
- Patient names must always use `headline-md` for immediate recognition during rapid scrolling.

## Layout & Spacing

The system follows a **Fixed Grid** model for desktop, centering a 1280px container on the screen to maintain focus. 

- **Desktop (1024px+):** Employs a 256px (64rem) fixed sidebar and a fluid content area within the max-width container. 
- **Mobile:** Transition to a full-width fluid layout with a persistent bottom navigation bar.
- **Vertical Rhythm:** Spacing is strictly based on an 8px base unit. `stack-md` (16px) is the standard for internal card padding, while `stack-lg` (24px) separates major layout sections.

## Elevation & Depth

Visual hierarchy is primarily established through **Tonal Layers** and **Low-Contrast Outlines**, with elevation used sparingly to signal interactivity.

1.  **Canvas:** Background is `#f7f9fb`.
2.  **Surfaces:** Navigation and cards use `#ffffff` with `#c2c6d2` (1px) outlines.
3.  **Shadows:** Card elements use a subtle `shadow-sm` (0 1px 3px 0 rgba(0,0,0,0.1)) to lift them slightly from the canvas. 
4.  **Interaction Depth:** On hover, card borders transition from the neutral outline to the `primary` color, providing clear affordance without shifting the layout.

## Shapes

The shape language is **Soft and Efficient**. 

- **Standard Elements:** Use a 4px (0.25rem) radius for secondary buttons and small inputs.
- **Containers/Cards:** Use a 12px (0.75rem) radius to feel modern and accessible.
- **Functional Pills:** Status badges and search bars use a `full` (9999px) radius to distinguish them from structural content.
- **Avatars:** Patient and staff profile images use a 8px radius or full circle depending on the context (lists vs. profiles).

## Components

### Buttons
- **Primary:** Solid background (`primary`), white text, bold weight. High-contrast and easily identifiable.
- **Secondary/Action:** Solid background (`secondary`), white text. Used for "Queue" or constructive actions.
- **Ghost/Tertiary:** No background, primary-colored text. Used for "View Details" to reduce visual noise in lists.

### Cards
Cards are the primary unit of information. They must include a top header bar with a low-opacity background tint (`surface-container-low/30`) to separate the facility origin from the patient data. Critical items must feature a 6px vertical "accent bar" on the left edge in the `status-critical` color.

### Status Chips
Status indicators must be pill-shaped.
- **Critical (ER/Trauma):** `status-critical-container` background with `status-critical` text and a pulsing dot icon.
- **New:** `status-new` background with white text.

### Inputs
Search inputs should be pill-shaped with a background of `surface-container-low` and no border, becoming outlined with a 2px stroke on focus.