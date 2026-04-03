import { Feed, posturlserver } from "./helperfeeds.js";

export class friends {
  constructor(user_id = null) {
    this.user_id = user_id ?? localStorage.getItem("user_id");
  }

  async getFriends(id) {
    const fetchApiClass = new Feed();
    return await fetchApiClass.fetchApi(posturlserver, {
      action: "getFriends",
      user:this.user_id,
      type: "friends",
    });
  }



      }