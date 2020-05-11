<template>
  <div id="editor">
    <mavon-editor ref="md" imgAdd="$imgAdd" style="height: 100%"></mavon-editor>
  </div>
</template>
<script>
import { mavonEditor } from "mavon-editor";
import "mavon-editor/dist/css/index.css";
import { addImage } from "@/api/article";

export default {
  name: "editor",
  components: {
    "mavon-editor": mavonEditor
  },
  methods: {
    async addImage(data) {
      return await addImage(data);
    },
    // 绑定@imgAdd event
    $imgAdd(pos, $file) {
      // 第一步.将图片上传到服务器.
      var formdata = new FormData();
      formdata.append("image", $file);
      this.addImage(formdata).then(res => {
        console.log(res);
        if (res.url) {
          this.$refs.md.$img2Url(pos, res.url);
        } else {
          this.$message({
            type: "error",
            message: "上传图片失败"
          });
        }
      });
      //    axios({
      //        url: '/dev',
      //        method: 'post',
      //        data: formdata,
      //        headers: { 'Content-Type': 'multipart/form-data' },
      //    }).then((url) => {
      //        // 第二步.将返回的url替换到文本原位置![...](0) -> ![...](url)
      //        // $vm.$img2Url 详情见本页末尾
      //        $vm.$img2Url(pos, url);
      //    })
    }
  }
};
</script>
<style>
#editor {
  margin: auto;
  width: 100%;
  height: 580px;
}
</style>