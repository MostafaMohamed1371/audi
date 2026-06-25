import { createMediaDetailPage } from "@/lib/media-detail-page";

const { generateStaticParams, default: MediaNewsletterDetailPage } =
  createMediaDetailPage("newsletter");

export { generateStaticParams };
export default MediaNewsletterDetailPage;
