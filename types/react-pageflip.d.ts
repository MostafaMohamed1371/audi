declare module "react-pageflip" {
  import type { ReactNode, Ref } from "react";

  export type PageFlipEvent = {
    data: number;
    object: unknown;
  };

  export type HTMLFlipBookProps = {
    width: number;
    height: number;
    size?: "fixed" | "stretch";
    minWidth?: number;
    maxWidth?: number;
    minHeight?: number;
    maxHeight?: number;
    drawShadow?: boolean;
    maxShadowOpacity?: number;
    showCover?: boolean;
    mobileScrollSupport?: boolean;
    usePortrait?: boolean;
    startPage?: number;
    flippingTime?: number;
    renderOnlyPageLengthChange?: boolean;
    className?: string;
    style?: React.CSSProperties;
    children: ReactNode;
    ref?: Ref<unknown>;
    onFlip?: (event: PageFlipEvent) => void;
  };

  const HTMLFlipBook: React.FC<HTMLFlipBookProps>;
  export default HTMLFlipBook;
}
