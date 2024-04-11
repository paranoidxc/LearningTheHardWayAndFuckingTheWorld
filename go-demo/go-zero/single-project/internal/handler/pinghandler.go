package handler

import (
	"net/http"
	"single-project/internal/logic"
	"single-project/internal/svc"
	"single-project/response"
)

func pingHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {

		l := logic.NewPingLogic(r.Context(), svcCtx)
		err := l.Ping()
		response.Response(w, nil, err)

	}
}
