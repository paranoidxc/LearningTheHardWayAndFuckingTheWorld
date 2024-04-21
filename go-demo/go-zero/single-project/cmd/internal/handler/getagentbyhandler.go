package handler

import (
	"net/http"
	"single-project/cmd/internal/logic"
	"single-project/cmd/internal/svc"
	"single-project/cmd/internal/types"

	"github.com/zeromicro/go-zero/rest/httpx"
)

func GetAgentByHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.AgentListReq
		if err := httpx.Parse(r, &req); err != nil {
			httpx.ErrorCtx(r.Context(), w, err)
			return
		}

		l := logic.NewGetAgentByLogic(r.Context(), svcCtx)
		resp, err := l.GetAgentBy(&req)
		if err != nil {
			httpx.ErrorCtx(r.Context(), w, err)
		} else {
			httpx.OkJsonCtx(r.Context(), w, resp)
		}
	}
}
