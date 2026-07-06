export interface SpritesMap {
  common:
    | 'arrow'
    | 'cancel'
    | 'check'
    | 'check-circle'
    | 'circle-arrow'
    | 'clear'
    | 'close-toast'
    | 'closeModal'
    | 'copy'
    | 'drop-down'
    | 'full-screen'
    | 'logo'
    | 'logs'
    | 'refresh'
    | 'rocket-launch'
    | 'schedule'
    | 'select-icon'
    | 'send'
    | 'slider-arrow'
    | 'support'
    | 'trending-flat'
    | 'verified';
}

export const SPRITES_META: { [K in keyof SpritesMap]: SpritesMap[K][] } = {
  common: [
    'arrow',
    'cancel',
    'check',
    'check-circle',
    'circle-arrow',
    'clear',
    'close-toast',
    'closeModal',
    'copy',
    'drop-down',
    'full-screen',
    'logo',
    'logs',
    'refresh',
    'rocket-launch',
    'schedule',
    'select-icon',
    'send',
    'slider-arrow',
    'support',
    'trending-flat',
    'verified',
  ],
};
