import { posturlserver } from "./helperfeeds.js";
import { Feed } from "./helperfeeds.js";

export class Auth {
  constructor(user_id = null) {
    this.user_id = user_id ?? localStorage.getItem("user_id");
  }

  async getuser(id = null, type = "you") {
    const fetchApiClass = new Feed(id ?? this.user_id);

    return await fetchApiClass.fetchApi(posturlserver, {
      action: "getuser",
      user: id ?? this.user_id,
      type: type,
    });
  }


   async loginer(username, password) {
      const fetchApiClass = new Feed();
          const res = await fetchApiClass.fetchApi(posturlserver, {
              action: "login",
              username: username,
              password: password
          });
          return res;
      }
  
      async register(firstname, lastname, email, phone, password, dob, gender) {
          const fetchApiClass = new Feed();
          const res = await fetchApiClass.fetchApi(posturlserver, {
              action: "register",
              firstname: firstname,
              lastname:lastname,
              email: email,
              phone: phone,
              password: password,
              dob: dob,
              gender: gender
          });
          return res;
      }


      


}