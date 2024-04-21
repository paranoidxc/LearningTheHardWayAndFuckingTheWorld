package handler

import (
	"net/http"
	"single-project/cmd/common/response"
	"single-project/cmd/internal/logic"
	"single-project/cmd/internal/svc"
)

func pingHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {

		l := logic.NewPingLogic(r.Context(), svcCtx)
		err := l.Ping()
		response.Response(w, nil, err)

	}
}
