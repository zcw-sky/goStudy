package main

import (
	"fmt"
	"log"
	"net/http"
	"os"
	"path/filepath"

	"github.com/gin-gonic/gin"
)

func login(ctx *gin.Context) {
	ck, err := ctx.Cookie("go_userName")
	log.Println(ck, err)
	ctx.HTML(http.StatusOK, "login.html", gin.H{})
}

func login1(ctx *gin.Context) {

	userName := ctx.PostForm("username")
	passWord := ctx.PostForm("password")
	arr := ctx.PostFormArray("hobby")
	//获取文件
	file, err := ctx.FormFile("avatar")
	if err != nil {
		fmt.Println(err)
	}

	// 创建文件保存路径
	if err := os.MkdirAll("uploads", os.ModePerm); err != nil {
		//ctx.String(http.StatusInternalServerError, fmt.Sprintf("upload file err: %s", err.Error()))
		log.Println(err)
	}

	// 保存文件
	savePath := filepath.Join("uploads", file.Filename)
	if err := ctx.SaveUploadedFile(file, savePath); err != nil {
		fmt.Println(err)
	}
	ctx.SetCookie("go_userName", userName, 3600, "/", "localhost", false, true)
	ctx.JSON(200, gin.H{
		"code":     http.StatusOK,
		"msg":      "v1登录成功",
		"userName": userName,
		"passWord": passWord,
		"hobby":    arr,
		"file":     file,
	})
}
func main() {
	fmt.Println("Hello World")
	r := gin.Default()
	r.LoadHTMLGlob("temp/*")
	//路由
	r.GET("/login", login)
	//路由分组
	v1 := r.Group("/v1")
	{
		v1.POST("/login", login1)
	}
	r.Run("0.0.0.0:8080")
}
