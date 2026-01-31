import { Link } from "react-router-dom";

export default function Sidebar(){
    const sidebarMenu = [
//   {icon: "ri-user-3-fill", label: "Blossom Lanta",link: "/profile"},
//   {icon: "ri-history-fill",label: "Memories",link: "/memories"},
//   {icon: "ri-bookmark-fill",label: "Saved",link: "/saved"},
  {icon: "ri-add-circle-fill",label: "Create Feeds",link: "/create"},
  {icon: "ri-group-fill",label: "Friends",link: "/friends"},
  {icon: "ri-group-2-fill",label: "Groups",link: "/groups"},
  {icon: "ri-video-fill",label: "Video",link: "/video"},
  {icon: "ri-store-2-fill", label: "Marketplace",link: "/marketplace"},
//   {icon: "ri-calendar-event-fill", label: "Events",link: "/events"},
//   {icon: "ri-bar-chart-box-fill", label: "Ads Manager",link: "/ads-manager"},
//   {icon: "ri-gamepad-fill",label: "Play games",link: "/games"},
//   {icon: "ri-arrow-down-s-line",label: "See more",link: "/more"}
];

   return(
         
            sidebarMenu.map((item, index) => (
            <Link
                key={index}
                to={item.link}
                className="d-flex align-items-center gap-3 py-2 text-decoration-none text-dark"
            >
                <i className={`${item.icon} fs-4`} />
                <span>{item.label}</span>
            </Link>
            ))
   )
}