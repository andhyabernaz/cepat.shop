
export function getApiUrl(state) {
  return state.API_URL
}
export function isModeDesktop(state) {
  if(state.window_width < 1024) {
    return false
  }
  if (state.config && state.config.mode_desktop) {
    return true
  }
  return false
}
export function isMobileWidth(state) {
  if (state.window_width < 1024) {
    return true
  }
  return false
}

export function getTheme(state) {

  // if (Platform.is.desktop && state.config && state.config.mode_desktop) {
  if (state.window_width >= 1024 && state.config && state.config.mode_desktop) {
    return 'desktop'
  }

  if (state.config && state.config.theme) {
    return state.config.theme
  }
  return 'default'
}

export function getHeaderColorBrand(state) {
  const hexCode = state.config.theme_color.charAt(0) === '#'
    ? state.config.theme_color.substr(1, 6)
    : state.config.theme_color;

  const hexR = parseInt(hexCode.substr(0, 2), 16);
  const hexG = parseInt(hexCode.substr(2, 2), 16);
  const hexB = parseInt(hexCode.substr(4, 2), 16);
  // Gets the average value of the colors
  const contrastRatio = (hexR + hexG + hexB) / (255 * 3);

  return contrastRatio >= 0.75
    ? 'bg-brand text-grey-9'
    : 'bg-brand text-white';
}
