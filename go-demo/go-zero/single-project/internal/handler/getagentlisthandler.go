package handler

import (
	"net/http"

	"github.com/zeromicro/go-zero/rest/httpx"
	"single-project/internal/logic"
	"single-project/internal/svc"
	"single-project/internal/types"
)

func GetAgentListHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.AgentListReq
		l := logic.NewGetAgentListLogic(r.Context(), svcCtx)
		resp, err := l.GetAgentList(&req)
		if err != nil {
			httpx.ErrorCtx(r.Context(), w, err)
		} else {
			httpx.OkJsonCtx(r.Context(), w, resp)
		}
	}
}
