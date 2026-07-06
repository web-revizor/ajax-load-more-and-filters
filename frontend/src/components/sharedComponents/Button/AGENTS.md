# Button Component DOX (frontend/src/components/Button/)

## Purpose
A universal button component that supports various styles (variants) and states, ensuring a consistent look for all interactive UI elements.

## Ownership
Frontend Development team.

## Local Contracts
- **Interface**:
    - `children`: Button text or icon.
    - `variant`: Button style (e.g., `primary`, `chat`, `secondary`).
    - `onClick`: Click handler function.
    - `className`: Additional classes for customization.
- **States**: Must visually represent `hover` and `active` states.

## Work Guidance
- Use `clsx` or `tailwind-merge` for dynamic class formation based on the variant.
- Ensure accessibility support via the standard `<button>` tag.

## Verification
- Verify correct rendering of all available `variants`.
- Ensure `onClick` is triggered correctly.
- Verify visual feedback on hover.
