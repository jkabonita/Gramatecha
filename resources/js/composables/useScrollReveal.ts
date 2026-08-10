import { onMounted, onUnmounted, type Ref } from 'vue';

/**
 * Attaches an IntersectionObserver to the given elements (or a container ref)
 * and adds the `is-visible` class when they enter the viewport.
 *
 * Usage:
 *   const el = ref<HTMLElement | null>(null);
 *   useScrollReveal(el);
 *
 * Or pass an array of refs:
 *   useScrollReveal([el1, el2, el3]);
 */
export function useScrollReveal(
    targets: Ref<HTMLElement | null> | Ref<HTMLElement | null>[],
    options: IntersectionObserverInit = { threshold: 0.15 },
) {
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer?.unobserve(entry.target); // fire only once
                }
            });
        }, options);

        const els = Array.isArray(targets) ? targets : [targets];
        els.forEach((ref) => {
            if (ref.value) observer!.observe(ref.value);
        });
    });

    onUnmounted(() => observer?.disconnect());
}

/**
 * Observe all children of a container element, applying staggered delay via
 * CSS custom property --stagger (0, 1, 2, …).
 */
export function useStaggerReveal(
    container: Ref<HTMLElement | null>,
    childSelector = ':scope > *',
    options: IntersectionObserverInit = { threshold: 0.1 },
) {
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (!container.value) return;

        const children = Array.from(container.value.querySelectorAll(childSelector)) as HTMLElement[];
        children.forEach((el, i) => {
            el.style.setProperty('--stagger', String(i));
        });

        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer?.unobserve(entry.target);
                }
            });
        }, options);

        children.forEach((el) => observer!.observe(el));
    });

    onUnmounted(() => observer?.disconnect());
}
