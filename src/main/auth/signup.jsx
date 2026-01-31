import { Link } from "react-router-dom";

export default function Signup(){
    return(
        <div className="position-fixed top-0 bottom-0 w-100 h-100 overflow-auto">
            <div className="container my-4 d-flex h-100 justify-content-center">
                <div className="col-4 mx-auto">
                    <div className="text-center my-3">
                        <h1 className="text-primary fs-2 fw-bolder text-capitalize" style={{fontSize:"60px"}}>monieflow</h1>
                    </div>
                    <div className="bg-white shadow rounded py-4 w-100">
                        <h2 className="text-center">Create a new account</h2>
                        <div className="text-center">It's quick and easy.</div>
                        <hr />
                        <div className="px-3 my-2 d-flex gap-3">
                            <input type="text" className="form-control" placeholder="Firstname" />
                            <input type="text" className="form-control" placeholder="Surname" />
                        </div>
                        <div className="px-3 my-2">
                            <label htmlFor="">Date of birth <span className="ri-question-fill"></span></label>
                            <div className="d-flex gap-3">
                                <select name="" id="" className="form-control">
                                        {Array.from({ length: 31 }, (_, idx) => {
                                        const day = idx + 1;
                                        return (
                                            <option key={day} value={day}>{day}</option>
                                        );
                                    })}
                                </select>
                                <select name="" id="" className="form-control text-capitalize">
                                        {
                                            ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'].map((e,key)=>{
                                                return <option key={key} value={key+1}>{e}</option>
                                            })
                                        }
                                </select>

                                <select name="" id="" className="form-control">
                                      {Array.from({ length: 2024 - 1905 + 1 }, (_, idx) => {
                                        const year = 2024 - idx;
                                        return(<option key={year} value={year}>{year}</option>)
                                    })}
                                </select>
                            </div>
                            <div className="my-2">
                                <div className="text-capitalize">gender <span className="ri-question-fill"></span></div>
                                <div className="d-flex gap-3 align-items-center">
                                    <span className="w-100 border d-flex gap-2 p-2 rounded">
                                        <input type="radio" name="gender" id="male" />
                                        <label htmlFor="male">Male</label>
                                    </span>
                                    <span className="w-100 border d-flex gap-2 p-2 rounded">
                                        <input type="radio" name="gender" id="female" />
                                        <label htmlFor="female">Female</label>
                                    </span>
                                    <span className="w-100 border d-flex gap-2 p-2 rounded">
                                        <input type="radio" name="gender" id="custom" />
                                        <label htmlFor="custom">Custom</label>
                                    </span>
                                </div>
                            </div>
                            <div className="my-2">
                                <input type="text" className="form-control" placeholder="Mobile number or email address" />
                            </div>
                            <div className="my-2">
                                <input type="password" className="form-control" placeholder="New password" />
                            </div>
                            <p style={{fontSize:"small"}} className="my-3 text-muted">
                                People who use our service may have uploaded your contact information to Facebook. 
                                <Link to="/learn-more" className="text-decoration-none">Learn more.</Link>
                            </p>
                            <p style={{fontSize:"small"}} className="my-3 text-muted">
                                By clicking Sign up, you agree to our Terms, Privacy Policy and Cookies Policy. You may receive SMS notifications from us and can opt out at any time.
                            </p>
                            <p className="text-center">
                                <button className="btn py-2 btn-success px-5 fs-5">signup</button>
                            </p>
                            <p className="text-center">
                                <Link to="/" className="text-decoration-none fs-5 fw-medium">Already have an account?</Link>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div className="bg-white py-3">
           <div className="col-8 mx-auto">
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