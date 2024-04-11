package handler

import (
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
	"single-project/cmd/common/response"
	"single-project/cmd/internal/logic"
	"single-project/cmd/internal/svc"
	"single-project/cmd/internal/types"
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
