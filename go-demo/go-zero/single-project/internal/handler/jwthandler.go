package handler

import (
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
	"single-project/internal/logic"
	"single-project/internal/svc"
	"single-project/internal/types"
	"single-project/response"
)

func JwtHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.JwtTokenRequest
		if err := httpx.Parse(r, &req); err != nil {
			httpx.Error(w, err)
			return
		}

		l := logic.NewJwtLogic(r.Context(), svcCtx)
		resp, err := l.Jwt(&req)
		response.Response(w, resp, err)

	}
}
