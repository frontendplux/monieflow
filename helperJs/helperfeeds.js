
export const posturlserver =
  window.location.hostname === "localhost" || window.location.hostname === "172.20.10.10"
    ? "http://" + window.location.hostname + ":3000/api/main/index.php"
    : window.location.origin + "/api/main/index.php";


export class Feed {
  constructor(user_id = null) {
    this.user_id = user_id ?? localStorage.getItem("user_id");
  }


  async fetchApi(url, data, formdata = false) {
        const fa = await fetch(url, {
            method: "POST",
            headers: formdata ? {} : { "Content-Type": "application/json" },
            body: formdata ? data : JSON.stringify(data)
        });
        const response = await fa.text();
        console.log(response);
        return JSON.parse(response);
    }

  async createFeedsx(fd, edit = false, post_id = null) {
    fd.append("user", this.user_id);
    fd.append("action", "createFeeds");

    if (edit && post_id) {
      fd.append("edit", true);
      fd.append("post_id", post_id);
    }

    return await this.fetchApi(posturlserver, fd, true);
  }

  // async fetchFeeds(post_id = 0, offset = 0, limit = 20) {
  //   return await this.fetchApi(posturlserver, {
  //     action: "fetchFeeds",
  //     user:this.user_id,
  //     type: post_id === 0 ? "all" : post_id,
  //     offset,
  //     limit,
  //   });
  // }

   async fetchFeeds(id = false, offset = 0, limit = 20, currency = "USD") {
        return await this.fetchApi(posturlserver, {
            action: "fetchFeeds",
            user: this.user_id,
            type: id ? id : 'all',
            offset: offset,
            currency:currency,
            limit: limit,
        });
   }

  async fetchComment() {}

  async removeOrdeletePost() {}

  async share() {}

  async fetchReply() {}

  async like() {}

  async likeComment() {}

  async sendTips() {}

  async reply() {}

  async replyComment() {}

  async removeOrDeleteComment() {}
}
