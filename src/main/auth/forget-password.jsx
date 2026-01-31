import { Link } from "react-router-dom";

export default function ForgetPassword(){
    return(
        <div >
            <header className="bg-white py-3" >
                <div className="d-flex container bg-white justify-content-between align-items-center">
                    <h1 className="m-0 text-primary fs-3">monieflow</h1>
                    <div>
                        <form action="" className="d-flex gap-3 align-items-center" method="post">
                            <input type="email" className="form-control" name="email" placeholder="Email or Phone" required />
                            <input type="password" className="form-control" name="password" id="password" placeholder="New Password" required />
                            <button className="border btn btn-primary p-2 px-3" type="submit">Log&nbsp;In</button>
                            <Link to="/signup" className="text-decoration-none text-primary">Forgotten&nbsp;account?</Link>
                        </form>
                    </div>
                </div>
            </header>
           <div className="">
                <div className="col-4 bg-white shadow-sm py-2 rounded mx-auto my-5">
                    <div className="fw-bold px-3 fs-5">Find Your Account</div>
                    <hr />
                    <div className="px-3 fs-5">Please enter your email address or mobile number to search for your account.</div>
                    <div className="my-2 px-3">
                        <input type="text" className="form-control p-3" placeholder="Email address or phone number"  />
                    </div>
                    <hr />
                    <div className="px-3 text-end">
                        <button className="btn btn-primary px-4 p-2 me-3">Search</button>
                        <Link to="/" className="btn btn-secondary px-4 p-2 text-decoration-none text-white">Cancel</Link>
                    </div>
                </div>
           </div>
            <div className="bg-white py-5">
           <div className="col-8 mx-auto my-5 pt-5">
                {
               [{ name: "English (US)", link:"en" }, { name: "Hausa", link:"ha" }, { name: "Français (France)", link:"fr" }, { name: "Português (Brasil)", link:"pt-br" }, { name: "Español", link:"es" }, { name: "العربية", link:"ar" }, { name: "Bahasa Indonesia", link:"id" }, { name: "Deutsch", link:"de" }, { name: "日本語", link:"ja" }, { name: "Italiano", link:"it" }, { name: "हिन्दी", link:"hi" }].map((item, index) => (
                   <Link key={index} to="#" className="text-decoration-none text-muted me-3">{item.name}</Link>
               ))
            }
            <hr />
            {
                [{ name: "Sign up", link:"signup"},{ name: "Log in", link:"login"},{ name: "About", link:"about" }, { name: "Help", link:"help" }, { name: "More", link:"more" }].map((item, index) => (
                   <Link key={index} to="#" className="text-decoration-none text-muted me-2">{item.name}</Link>
               ))}
{/* MessengerFacebook LiteVideoMeta PayMeta StoreMeta QuestRay-Ban MetaMeta AIMeta AI more contentInstagramThreadsVoting Information CentrePrivacy PolicyPrivacy CentreAboutCreate adCreate PageDevelopersCareersCookiesAdChoicesTermsHelpContact uploading and non-usersSettingsActivity log */}
                <div>Meta © 2026</div>
    </div>
           </div>
        </div>
    )
}