---
name: Healthcare Utility
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
  on-surface-variant: '#424751'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#737782'
  outline-variant: '#c2c6d2'
  surface-tint: '#295ea7'
  primary: '#003166'
  on-primary: '#ffffff'
  primary-container: '#00478f'
  on-primary-container: '#8fb8ff'
  inverse-primary: '#aac7ff'
  secondary: '#29695b'
  on-secondary: '#ffffff'
  secondary-container: '#acedda'
  on-secondary-container: '#2e6d5f'
  tertiary: '#00383e'
  on-tertiary: '#ffffff'
  tertiary-container: '#005058'
  on-tertiary-container: '#43c7d8'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d6e3ff'
  primary-fixed-dim: '#aac7ff'
  on-primary-fixed: '#001b3e'
  on-primary-fixed-variant: '#00468c'
  secondary-fixed: '#afefdd'
  secondary-fixed-dim: '#94d3c1'
  on-secondary-fixed: '#00201a'
  on-secondary-fixed-variant: '#065043'
  tertiary-fixed: '#93f1ff'
  tertiary-fixed-dim: '#58d7e8'
  on-tertiary-fixed: '#001f23'
  on-tertiary-fixed-variant: '#004f57'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
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
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-max: 1280px
  gutter: 24px
  margin-desktop: 40px
  margin-mobile: 16px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 24px
---

## Brand & Style
The brand personality is rooted in efficiency, clarity, and medical reliability. It serves as a bridge for critical communication in a fast-paced environment, requiring a UI that prioritizes information density and legibility without feeling overwhelming.

The design style is **Corporate / Modern** with a focus on high-clarity information architecture. It utilizes a predominantly white environment to ensure a sterile and professional feel, punctuated by intentional color signals. The aesthetic leverages structural cards and subtle borders to create a organized, modular interface that translates seamlessly from its mobile origins to a comprehensive desktop dashboard.

## Colors
This design system employs a systematic color palette designed for high-stakes healthcare environments.

*   **Primary (Navy Blue):** Used for navigation, brand identity, and high-priority actions like "Done."
*   **Secondary (Dark Teal):** Utilized for task-specific success actions, such as "Queue," providing a distinct visual separation from navigational elements.
*   **Tertiary (Bright Cyan):** Used for status indicators and active navigation states to provide high-visibility highlights.
*   **Neutral & Backgrounds:** A clean `#F8FAFC` base provides a soft contrast against pure white `#FFFFFF` cards.
*   **Semantic Accents:** Red is reserved for urgent clinical tags (e.g., "ER OB"), ensuring immediate visual recognition of critical patient status.

## Typography
The typography is functional and utilitarian. **Manrope** is used for headlines to provide a modern, professional structure, while **Inter** is used for all data-heavy body text and labels to maximize legibility.

Patient names and category headers should use uppercase styling to differentiate them from descriptive metadata. In the desktop transition, ensure that headline-md is used for section titles to maintain a clear hierarchy on larger displays.

## Layout & Spacing
The layout follows a **Fluid Grid** model for mobile and a **Fixed Grid** model (max-width: 1280px) for desktop. 

- **Desktop Transition:** On desktop, the central feed of cards should be centered within the 1280px container, with supplementary navigation or filters moving to a fixed left or right sidebar.
- **Rhythm:** Use a strict 8px-based spacing system. All card padding should be a minimum of 16px (stack-md) to ensure content does not feel cramped.
- **Grouping:** Use stack-sm (8px) for related metadata (e.g., patient age and gender) and stack-md (16px) for separating logical blocks within a card.

## Elevation & Depth
This design system avoids heavy shadows in favor of **Tonal Layers** and **Low-Contrast Outlines**.

*   **Base Surface:** The background uses the neutral `#F8FAFC`.
*   **Cards:** Elements are placed on pure white `#FFFFFF` surfaces with a 1px solid border in `#E2E8F0`. 
*   **Interaction:** No elevation (shadows) should be used. Instead, use subtle background color shifts (e.g., light blue tint) or border color changes to indicate hover or focus states.
*   **Depth Signaling:** Vertical red accents on the left edge of cards are used to denote "New" or "High Priority" status, providing a clear visual priority without adding physical depth.

## Shapes
The shape language is consistently **Rounded** (8px / 0.5rem) to balance the clinical nature of the app with an approachable, modern feel.

*   **Cards and Inputs:** Use a standard 12px (0.75rem) corner radius.
*   **Buttons:** Use a 8px (0.5rem) radius for a sturdy, professional appearance.
*   **Avatar:** Circular (pill) masks should be used for patient and user profile photos.
*   **Tags/Chips:** Use a fully rounded pill shape (100px) for status indicators like "ER OB" or "NEW."

## Components

### Buttons
*   **Primary:** Solid Navy Blue (`#00478F`) or Dark Teal (`#004D40`) with white text and centered icons.
*   **Secondary/Outline:** 1.5px border of the primary color with the corresponding text color.
*   **Icons:** Use weighted, functional line icons (e.g., `plus-square` for hospitals, `file-text` for complaints).

### Cards
*   Cards are white with a light grey border. 
*   Header sections within cards (e.g., Hospital name) should be separated by a subtle horizontal divider or a distinct background tint.
*   Action buttons should be placed at the bottom of the card, often spanning the full width in a 50/50 split on desktop.

### Input Fields
*   Search bars use a fully rounded (pill) shape with a subtle grey background and a search icon prefix.
*   Standard form fields should use a 1px border and a 12px radius, with labels positioned clearly above the input.

### Chips & Tags
*   Status tags (e.g., "New") should use a tertiary background (`#64E2F3`) with dark navy text for high visibility.
*   Medical urgency tags use a soft red background with red text and a leading dot icon to signal status.

### Lists
*   List items in detail views should feature a leading icon contained within a soft-blue tinted square for better scanning.