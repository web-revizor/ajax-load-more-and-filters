# SlideDown Component DOX (frontend/src/components/SlideDown/)

## Purpose
An animation wrapper that provides a "slide-down" effect for expanding content, such as tool result details or collapsible sections.

## Ownership
Frontend Development team.

## Local Contracts
- **Interface**: Accepts `isOpen` boolean and `children`.
- **Animation**: Must use CSS transitions for `height` or `transform` to ensure smoothness.
- **Layout**: Should not cause sudden jumps in the layout when opening/closing.

## Work Guidance
- Use `max-height` transitions for elements with dynamic content size.
- Ensure the animation is disabled or fast for users who prefer reduced motion.

## Verification
- Verify the smoothness of the slide-down and slide-up animations.
- Check that the content is fully accessible and not clipped when expanded.
