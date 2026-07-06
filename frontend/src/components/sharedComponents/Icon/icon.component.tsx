import clsx from 'clsx';
import { SVGProps } from 'react';
import { SpritesMap } from './sprite-info';

export type SpriteKey = {
  [Key in keyof SpritesMap]: `${Key}/${SpritesMap[Key]}`;
}[keyof SpritesMap];

export interface IconProps extends Omit<
  SVGProps<SVGSVGElement>,
  'name' | 'type'
> {
  name: SpriteKey;
  width?: number;
  height?: number;
}

export function Icon({
  name,
  width,
  height,
  className,
  viewBox,
  ...props
}: IconProps) {
  const [spriteName, iconName] = name.split('/');
  return (
    <>
      <svg
        xmlns='http://www.w3.org/2000/svg'
        width={width}
        height={height}
        className={clsx('icon', className)}
        viewBox={viewBox}
        focusable='false'
        aria-hidden
        {...props}
      >
        <use href={`#icon-${iconName}`} />
      </svg>
    </>
  );
}
