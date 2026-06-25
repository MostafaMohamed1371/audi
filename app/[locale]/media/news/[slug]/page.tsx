import { createMediaDetailPage } from "@/lib/media-detail-page";

const { generateStaticParams, default: MediaNewsDetailPage } =
  createMediaDetailPage("news");

export { generateStaticParams };
export default MediaNewsDetailPage;
