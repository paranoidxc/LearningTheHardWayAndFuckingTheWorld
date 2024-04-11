package handler

import (
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
	"single-project/internal/logic"
	"single-project/internal/svc"
	"single-project/internal/types"
	"single-project/internal/validator"
)

func GetUserFindHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.UsertByIdReq
		if err := httpx.Parse(r, &req); err != nil {
			httpx.ErrorCtx(r.Context(), w, err)
			return
		}

		validateErr := validator.Validate(&req)
		if validateErr != nil {
			httpx.ErrorCtx(r.Context(), w, validateErr)
			return
		}

		l := logic.NewGetUserFindLogic(r.Context(), svcCtx)
		resp, err := l.GetUserFind(&req)
		if err != nil {
			httpx.ErrorCtx(r.Context(), w, err)
		} else {
			httpx.OkJsonCtx(r.Context(), w, resp)
		}
	}
}
