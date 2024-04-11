package handler

import (
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
	"single-project/internal/logic"
	"single-project/internal/svc"
	"single-project/internal/types"
	"single-project/response"
)

func GetUserListHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.UsertListReq
		if err := httpx.Parse(r, &req); err != nil {
			httpx.Error(w, err)
			return
		}

		l := logic.NewGetUserListLogic(r.Context(), svcCtx)
		resp, err := l.GetUserList(&req)
		response.Response(w, resp, err)

	}
}
