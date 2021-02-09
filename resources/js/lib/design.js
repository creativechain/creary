const BOOTSTRAP_SIZES = {
    XS: -1,
    SM: 576,
    MD: 768,
    LG: 992,
    XL: 1200,
};

const BOOTSTRAP_BREAKPOINTS = {
    xs: window.screen.width < BOOTSTRAP_SIZES.SM,
    sm: window.screen.width >= BOOTSTRAP_SIZES.SM && window.screen.width < BOOTSTRAP_SIZES.MD,
    md: window.screen.width >= BOOTSTRAP_SIZES.MD && window.screen.width < BOOTSTRAP_SIZES.LG,
    lg: window.screen.width >= BOOTSTRAP_SIZES.LG && window.screen.width < BOOTSTRAP_SIZES.XL,
    xl: window.screen.width >= BOOTSTRAP_SIZES.XL,
};

/**
 *
 * @param {string} breakpoint
 * @returns boolean
 */
function isBreakpoint(breakpoint) {
    return BOOTSTRAP_BREAKPOINTS[breakpoint];
}

module.exports = {
    BOOTSTRAP_SIZES,
    BOOTSTRAP_BREAKPOINTS,
    isBreakpoint,
};
